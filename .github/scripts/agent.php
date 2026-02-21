<?php
// 1. Grab the environment variables provided by GitHub Actions
$apiKey = getenv('GEMINI_API_KEY');
$githubToken = getenv('GITHUB_TOKEN');
$issueNumber = getenv('ISSUE_NUMBER');
$issueBody = getenv('ISSUE_BODY');
$repo = getenv('REPO_NAME');

// 2. The Agent's Prompt (Context + Task)
$systemPrompt = "You are the lead maintainer of a Laravel SMS notification package. 
Read the following GitHub issue. Determine if it is a bug or a feature request. 
Write a concise, polite reply acknowledging the issue. 
CRITICAL INSTRUCTION: If the user is struggling with payload formatting or a specific function, provide a very short, targeted PHP code snippet (under 15 lines) to help them troubleshoot or implement the fix. Do not write full files, only the relevant snippet.
Issue Text: " . $issueBody;

// 3. Call the Free Gemini API
$aiData = [
    "contents" => [
        ["parts" => [["text" => $systemPrompt]]]
    ]
];

$ch = curl_init("https://generativelanguage.googleapis.com/v1beta/models/gemini-3-flash-preview:generateContent?key=" . $apiKey);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($aiData));

$response = json_decode(curl_exec($ch), true);
$aiReply = $response['candidates'][0]['content']['parts'][0]['text'] ?? "Thanks for opening this issue! I will look into it shortly.";

// 4. Post the AI's response back to the GitHub Issue
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

echo "Agent successfully replied to issue #$issueNumber\n";
?>