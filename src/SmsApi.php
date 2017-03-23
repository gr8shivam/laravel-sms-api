<?php

namespace Gr8Shivam\SmsApi;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class SmsApi
{
    private $config = array();
    private $gateway;
    private $client;
    private $request = '';
    private $response = '';

    public function __construct() {
        $this->createClient();
    }

    private function loadDefaultGateway() {
        $default_acc = config('sms-api.default', null);
        if ($default_acc) {
            $this->gateway = $default_acc;
        }
        return $this;
    }

    protected function loadCredentialsFromConfig() {
        $gateway = $this->gateway;
        $config_name = 'sms-api.'.$gateway;
        $this->config = config($config_name);
        return $this;
    }

    protected function createClient() {
        $this->client = new Client;
        return $this;
    }

    public function getClient() {
        return $this->client;
    }

    public function gateway($gateway=''){
        $this->gateway = $gateway;
    }

    public function getUrl($mobile,$message) {
        $params = $this->config['params']['others'];
        $send_to_param_name = $this->config['params']['send_to_param_name'];
        $msg_param_name = $this->config['params']['msg_param_name'];
        $params[$send_to_param_name] = $mobile;
        $params[$msg_param_name] = $message;
        $url = $this->config['url'];
        foreach($params as $key=>$val) {
            $this->request.= $key."=".urlencode($val);
            $this->request.= "&";
        }
        $this->request = substr($this->request, 0, strlen($this->request)-1);
        return $url.$this->request;
    }

    public function sendMessage($to,$message) {
        if($this->gateway==''){
            $this->loadDefaultGateway();
        }
        $this->loadCredentialsFromConfig();
        $mobile = $this->config['add_code']?$this->addCountryCode($to):$to;
        if(is_array($mobile)){
            $mobile = $this->composeBulkMobile($mobile);
        }
        $this->response = $this->client->get($this->getUrl($mobile,$message))->getBody()->getContents();
        Log::info('SMS Gateway Response: '.$this->response);
        return $this;
    }

    private function composeBulkMobile($mobile) {
        return implode(',',$mobile);
    }

    private function addCountryCode($mobile) {
        if(is_array($mobile)){
            array_walk($mobile, function(&$value, $key) { $value = config('sms-api.country_code', '91').$value; });
            return $mobile;
        }
        return config('sms-api.country_code', '91').$mobile;
    }

    public function response(){
        return $this->response;
    }
}