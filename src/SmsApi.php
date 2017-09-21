<?php

namespace Gr8Shivam\SmsApi;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\RequestException;

class SmsApi
{
    private $config = array();
    private $gateway;
    private static $client=null;
    private $request = '';
    private $response = '';
    private $country_code=null;

    /**
     * SmsApi constructor.
     */
    public function __construct() {
        $this->createClient();
    }

    /**
     * Create new Guzzle Client
     *
     * @return $this
     */
    protected function createClient() {
        if(!self::$client){
            self::$client = new Client;
        }
        return $this;
    }

    /**
     * Load Default Gateway
     *
     * @return $this
     */
    private function loadDefaultGateway() {
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
    protected function loadCredentialsFromConfig() {
        $gateway = $this->gateway;
        $config_name = 'sms-api.'.$gateway;
        $this->config = config($config_name);
        return $this;
    }

    /**
     * Get Client
     *
     * @return GuzzleHttp\Client
     */
    public function getClient() {
        return self::$client;
    }

    /**
     * Set custom gateway
     *
     * @param string $gateway
     * @return $this
     */
    public function gateway($gateway=''){
        $this->gateway = $gateway;
        return $this;
    }

    /**
     * Set custom country code
     *
     * @param string $country_code
     * @return $this
     */
    public function countryCode($country_code=''){
        $this->country_code = $country_code;
        return $this;
    }

    /**
     * Add country code to mobile
     *
     * @param $mobile
     * @return array|string
     */
    private function addCountryCode($mobile) {
        if(!$this->country_code){
            $this->country_code=config('sms-api.country_code', '91');
        }
        if(is_array($mobile)){
            array_walk($mobile, function(&$value, $key) { $value = $this->country_code.$value; });
            return $mobile;
        }
        return $this->country_code.$mobile;
    }

    /**
     * For multiple mobiles
     *
     * @param $mobile
     * @return string
     */
    private function composeBulkMobile($mobile) {
        return implode(',',$mobile);
    }

    /**
     * Generate Request URL
     *
     * @param $mobile
     * @param $message
     * @param array $extra_params
     * @return string
     */
    private function getUrl($mobile, $message, $extra_params=null) {
        $params = $this->config['params']['others'];
        $send_to_param_name = $this->config['params']['send_to_param_name'];
        $msg_param_name = $this->config['params']['msg_param_name'];
        $params[$send_to_param_name] = $mobile;
        $params[$msg_param_name] = $message;
        $url = $this->config['url'];
        if($extra_params){
            $params = array_merge($params,$extra_params);
        }
        foreach($params as $key=>$val) {
            $this->request.= $key."=".urlencode($val);
            $this->request.= "&";
        }
        $this->request = substr($this->request, 0, strlen($this->request)-1);
        return $url.$this->request;
    }

    /**
     * Send message
     *
     * @param $to
     * @param $message
     * @param array $extra_params
     * @return $this
     */
    public function sendMessage($to, $message, $extra_params=null) {
        if($this->gateway==''){
            $this->loadDefaultGateway();
        }
        $this->loadCredentialsFromConfig();
        $mobile = $this->config['add_code']?$this->addCountryCode($to):$to;
        if(is_array($mobile)){
            $mobile = $this->composeBulkMobile($mobile);
        }
        try {
            $this->response = $this->getClient()->get($this->getUrl($mobile,$message,$extra_params))->getBody()->getContents();
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $this->response = $e->getResponseBodySummary($e->getResponse());
            }
        }
        Log::info('SMS Gateway Response: '.$this->response);
        return $this;
    }

    /**
     * Return Response
     *
     * @return string
     */
    public function response(){
        return $this->response;
    }
}