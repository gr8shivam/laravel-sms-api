# Integrate SMS API with Laravel
Laravel package to provide SMS API integration. Any SMS vendor that provides REST API can be used.

## Installation
Require this package with composer:
```
composer require gr8shivam/laravel-sms-api
```
Once the package is added, add the ServiceProvider to the providers array in ```config/app.php```:
```
Gr8Shivam\SmsApi\SmsApiServiceProvider::class,
```
Once done, publish the config to your config folder using:
```
php artisan vendor:publish --provider="Gr8Shivam\SmsApi\SmsApiServiceProvider"
```

## Configuration
Once the config file is published, open ```config/sms-api.php```

#### Global config
```country_code``` : The default country code to be used

```default``` : Default gateway 

#### Gateway Config
Use can define multiple gateway configs like this:-
```
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
```

## Usage
### Direct Use
Use the SmsApi class where you want to use it.
```
use Gr8Shivam\SmsApi\SmsApi;
```
Then create an object of the class:-
```
$sms = new SmsApi();
```
Then use the sendMessage method:-
```
$sms->sendMessage("TO","MESSAGE");
```

```TO```: Single mobile number or Multiple comma-separated mobile numbers
```MESSAGE```: Message to be sent

In order to use a different gateway, add ```->gateway('GATEWAY_NAME')``` like:-
```
$sms->sendMessage("TO","MESSAGE")->gateway('GATEWAY_NAME');
```

### Use in Notifications
Notification example:-
```
namespace App\Notifications;

use Gr8Shivam\SmsApi\Notifications\SmsApiChannel;
use Gr8Shivam\SmsApi\Notifications\SmsApiMessage;
use Illuminate\Notifications\Notification;

class ExampleNotification extends Notification
{
    public function via($notifiable)
    {
        return [SmsApiChannel::class];
    }
    
    public function toSmsApi($notifiable)
    {
        return (new SmsApiMessage)
            ->content("Hello");
    }
}
```
Add the method ```routeNotificationForSmsApi()``` to your Notifiable model :
```
public function routeNotificationForSmsApi()
    {
        return $this->phone; //Name of the field to be used as mobile
    }    
```

## Support
Feel free to post your issues in the issues section.

## Credits
Developed by [Shivam Agarwal](https://github.com/gr8shivam "Shivam Agarwal")

Thanks to [laravel-ovh-sms](https://github.com/MarceauKa/laravel-ovh-sms "laravel-ovh-sms") & [softon-sms](https://github.com/softon/sms "softon-sms")

## License
MIT
