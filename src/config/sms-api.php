<?php

return [

    'country_code' => '91', //Country code to be added
    'default' => env('SMS_API_DEFAULT_GATEWAY', 'msg91'), //Choose default gateway
    
//    Gateway Configuration
    'gateway_name' => [
        'url' => 'BaseUrl', //Base URL
        'params' => [
            'send_to_param_name' => '', //Send to Parameter Name
            'msg_param_name' => '', //Message Parameter Name
            'others' => [
                'param1' => '',
                'param2' => '',
                'param3' => '',
                //More params can be added
            ],
        ],
        'add_code' => true, //Include Country Code (true/false)
    ],
    
    
    /*
    *
    * Examples :- 
    *
    */
    
//    SMSNIX
    'smsnix' => [
        'url' => 'http://bulk.smsnix.in/vendorsms/pushsms.aspx?',
        'params' => [
            'send_to_param_name' => 'msisdn', //Send to Parameter Name
            'msg_param_name' => 'msg', //Message Parameter Name
            'others' => [
                'user' => '', //Your username
                'password' => '', //Your Password
                'sid' => '', //Your Sender ID
                'fl' => '0',
                'gwid' => '2',
            ],
        ],
        'add_code' => true, //Include Country Code
    ],

//    MSG91
    'msg91' => [
        'url' => 'https://control.msg91.com/api/v2/sendsms?',
        'params' => [
            'send_to_param_name' => 'mobiles', //Send to Parameter Name
            'msg_param_name' => 'message', //Message Parameter Name
            'others' => [
                'authkey' => '', //Your auth key
                'sender' => '', //Your Sender ID
                'route' => '4',
                'country' => '91',
            ],
        ],
        'add_code' => false, //Include Country Code
    ]

];