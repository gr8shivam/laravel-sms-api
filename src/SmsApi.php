<?php

namespace Gr8Shivam\SmsApi;


use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\RequestException;
use Gr8Shivam\SmsApi\Exception\InvalidMethodException;

class SmsApi
{
    private static $client = null;
    private $config = array();
    private $gateway;
    private $request = '';
    private $response = '';
    private $country_code = null;

    /**
     * SmsApi constructor.
     */
    public function __construct()
    {
        $this->createClient();
    }

    /**
     * Create new Guzzle Client
     *
     * @return $this
     */
    protected function createClient()
    {
        if (!self::$client) {
            self::$client = new Client;
        }
        return $this;
    }

    /**
     * Set custom gateway
     *
     * @param string $gateway
     * @return $this
     */
    public function gateway($gateway = '')
    {
        $this->gateway = $gateway;
        return $this;
    }

    /**
     * Set custom country code
     *
     * @param string $country_code
     * @return $this
     */
    public function countryCode($country_code = '')
    {
        $this->country_code = $country_code;
        return $this;
    }

    /**
     * Send message
     *
     * @param $to
     * @param $message
     * @param array $extra_params
     * @param array $extra_headers
     * @return $this
     * @throws InvalidMethodException
     */
    public function sendMessage($to, $message, $extra_params = null, $extra_headers = [])
    {
        if ($this->gateway == '') {
            $this->loadDefaultGateway();
        }
        $this->loadCredentialsFromConfig();

        $request_method = isset($this->config['method']) ? $this->config['method'] : 'GET';
        $url = $this->config['url'];

        $mobile = $this->config['add_code'] ? $this->addCountryCode($to) : $to;
        if (!(isset($this->config['json']) && $this->config['json'])) {
        	//Flatten Array if JSON false
            if (is_array($mobile)){
                $mobile = $this->composeBulkMobile($mobile);
            }
        }
        else{
        	//Transform to Array if JSON true
            if (!is_array($mobile)){
                $mobile = array($mobile);
            }
        }

        $params = $this->config['params']['others'];

        $headers = isset($this->config['headers']) ? $this->config['headers'] : [];

        //Check wrapper for JSON Payload
        $wrapper = isset($this->config['wrapper']) ? $this->config['wrapper'] : NULL;

        $send_to_param_name = $this->config['params']['send_to_param_name'];
        $msg_param_name = $this->config['params']['msg_param_name'];

        if ($wrapper) {
            $send_vars[$send_to_param_name] = $mobile;
            $send_vars[$msg_param_name] = $message;
        } else {
            $params[$send_to_param_name] = $mobile;
            $params[$msg_param_name] = $message;
        }

        if ($extra_params) {
            $params = array_merge($params, $extra_params);
        }

        if($extra_headers){
            $headers = array_merge($headers, $extra_headers);
        }

        try {
            //Build Request
            $request = new Request($request_method, $url);
            if ($request_method == "GET") {
                $promise = $this->getClient()->sendAsync(
                    $request,
                    [
                        'query' => $params,
                        'headers' => $headers
                    ]
                );
            } elseif ($request_method == "POST") {
                $payload = $wrapper ? array_merge(array($wrapper => array($send_vars)), $params) : $params;

                if ((isset($this->config['json']) && $this->config['json'])) {
                    $promise = $this->getClient()->sendAsync(
                        $request,
                        [
                            'json' => $payload,
                            'headers' => $headers
                        ]
                    );
                } else {
                    $promise = $this->getClient()->sendAsync(
                        $request,
                        [
                            'query' => $params,
                            'headers' => $headers
                        ]
                    );
                }
            } else {
                throw new InvalidMethodException(
                    sprintf("Only GET and POST methods allowed.")
                );
            }

            $this->response = $promise->wait()->getBody()->getContents();

        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $this->response = $e->getResponseBodySummary($e->getResponse());
            }
        }
        Log::info('SMS Gateway Response: ' . $this->response);
        return $this;
    }

    /**
     * Load Default Gateway
     *
     * @return $this
     */
    private function loadDefaultGateway()
    {
        $default_acc = config('sms-api.default', null);
        if ($default_acc) {
            $this->gateway = $default_acc;
        }
        return $this;
    }

    /**
     * Load Credentials from the selected Gateway
     *
     * @return $this
     */
    protected function loadCredentialsFromConfig()
    {
        $gateway = $this->gateway;
        $config_name = 'sms-api.' . $gateway;
        $this->config = config($config_name);
        return $this;
    }

    /**
     * Add country code to mobile
     *
     * @param $mobile
     * @return array|string
     */
    private function addCountryCode($mobile)
    {
        if (!$this->country_code) {
            $this->country_code = config('sms-api.country_code', '91');
        }
        if (is_array($mobile)) {
            array_walk($mobile, function (&$value, $key) {
                $value = $this->country_code . $value;
            });
            return $mobile;
        }
        return $this->country_code . $mobile;
    }

    /**
     * For multiple mobiles
     *
     * @param $mobile
     * @return string
     */
    private function composeBulkMobile($mobile)
    {
        return implode(',', $mobile);
    }

    /**
     * Get Client
     *
     * @return GuzzleHttp\Client
     */
    public function getClient()
    {
        return self::$client;
    }

    /**
     * Return Response
     *
     * @return string
     */
    public function response()
    {
        return $this->response;
    }
}