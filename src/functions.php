<?php

/*
 * Add helper function
 */

if (! function_exists('smsapi')) {

    /**
     * @param string $to
     * @param string $message
     * @param array $extra_params
     * @return mixed
     */
    function smsapi($to = null, $message = null, $extra_params=null)
    {
        $smsapi = app('smsapi');
        if (! (is_null($to) || is_null($message))) {
            return $smsapi->sendMessage($to,$message,$extra_params);
        }
        return $smsapi;
    }
}