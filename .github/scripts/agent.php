<?php
// 1. Grab the environment variables provided by GitHub Actions
$apiKey = getenv('GEMINI_API_KEY');
$githubToken = getenv('GITHUB_TOKEN');
$issueNumber = getenv('ISSUE_NUMBER');
$issueBody = getenv('ISSUE_BODY');
$repo = getenv('REPO_NAME');

// 2. Read context and build prompt
// Using @ to suppress warnings if the file doesn't exist, and providing a fallback
$packageDocs = @file_get_contents(__DIR__ . '/../../README.md') ?: "Documentation not found.";

$systemPrompt = "You are the lead maintainer of a Laravel SMS notification package. 
Read the following GitHub issue. Determine if it is a bug or a feature request. 
Write a concise, polite reply acknowledging the issue. 
CRITICAL INSTRUCTION: If the user needs help with code, provide a short PHP snippet (under 15 lines). 

Use the following official documentation as your ABSOLUTE SOURCE OF TRUTH for all code snippets. Do not invent functions that do not exist in this documentation:

--- PACKAGE DOCUMENTATION ---
" . $packageDocs . "
-----------------------------

Issue Text: " . $issueBody;

$aiData = [
    "contents" => [
        ["parts" => [["text" => $systemPrompt]]]
    ]
];
$jsonPayload = json_encode($aiData);

// 3. Call the Gemini API with Exponential Backoff Retry Logic
$apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3-flash-preview:generateContent?key=" . $apiKey;

$maxRetries = 3;
$attempt = 0;
$success = false;
$response = null;

while ($attempt < $maxRetries && !$success) {
    $attempt++;
    
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
    
    $rawResult = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // If the HTTP code is 200 (OK), parse the JSON
    if ($httpCode == 200) {
        $response = json_decode($rawResult, true);
        if (isset($response['candidates'])) {
            $success = true;
            break; // Break out of the while loop!
        }
    }
    
    // If we reach here, it failed. Print to the GitHub Action logs.
    echo "Attempt $attempt failed. HTTP Code: $httpCode\n";
    echo "Raw Result: $rawResult\n";
    
    // If we have retries left, wait before trying again
    if ($attempt < $maxRetries) {
        $sleepTime = pow(2, $attempt); // 2 seconds, then 4, then 8
        echo "Server busy. Retrying in $sleepTime seconds...\n";
        sleep($sleepTime);
    }
}

// 4. Handle the final result
if (!$success) {
    echo "🚨 API ERROR: Failed after $maxRetries attempts. 🚨\n";
    // A graceful fallback message so the user still gets acknowledged
    $aiReply = "Thanks for opening this issue! Our automated assistant is experiencing high server load right now, but a human maintainer will look into this shortly.";
} else {
    $aiReply = $response['candidates'][0]['content']['parts'][0]['text'];
}

// 5. Post the response back to GitHub
$githubUrl = "https://api.github.com/repos/{$repo}/issues/{$issueNumber}/comments";
$githubData = json_encode(["body" => "**AI Agent Reply:**\n\n" . $aiReply]);

$chGit = curl_init($githubUrl);
curl_setopt($chGit, CURLOPT_RETURNTRANSFER, true);
curl_setopt($chGit, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $githubToken,
    'User-Agent: PHP-Agent',
    'Accept: application/vnd.github.v3+json'
]);
curl_setopt($chGit, CURLOPT_POST, true);
curl_setopt($chGit, CURLOPT_POSTFIELDS, $githubData);
curl_exec($chGit);
curl_close($chGit);

echo "Agent successfully replied to issue #$issueNumber\n";
?>