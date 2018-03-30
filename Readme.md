# Laravel Passport Multi-Auth

Add multi-authentication support to #[Laravel Passport](https://github.com/laravel/passport/)

## Installation
With Composer
```bash
$ composer require yutianx/laravel-passport-multiauth
```

If you are using a Laravel version less than 5.5 you need to add the provider on config/app.php:
```php
    'providers' => [
        ...
        Yutianx\LPM\MultiAuthServiceProvider::class,
    ]
```

## Configuration

And your ```config/auth.php``` providers

Example:
```php
    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model'  => App\User::class,
            'client_id' => 2,
        ],
        'admins' => [
            'driver' => 'eloquent',
            'model'  => App\Admin::class,
            'client_id' => 4, 
        ]  
    ]
```

## Usage


#### Request

Add ```application/vnd.passport.provider_name``` to the HTTP Accept header

Example:

```http
GET /user HTTP/1.1

Host: example.com
X-Requested-With: XMLHttpRequest
Accept: application/json; application/vnd.passport.admins
Authorization: Bearer [TOKEN]
```

#### Token

Example:

```php
public function token()
{
    $client = new GuzzleHttp\Client();
    
    $response = $http->post('http://your-app.com/oauth/token', [
        'form_params' => [
            'grant_type' => 'password',
            'client_id' => \Yutianx\LPM\Facades\PassportMultiAuth::clientId(),
            'client_secret' => 'client-secret',
            'username' => 'taylor@laravel.com',
            'password' => 'my-password',
            'scope' => '',
        ],
    ]);
}
```
