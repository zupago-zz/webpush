# Web push notifications channel for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/zupago/webpush.svg?style=flat-square)](https://packagist.org/packages/zupago/webpush)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/zupago/webpush/master.svg?style=flat-square)](https://travis-ci.org/zupago/webpush)
[![StyleCI](https://styleci.io/repos/110953346/shield)](https://styleci.io/repos/110953346)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/9bc06cdd-6bd0-43d5-b8fb-9768e174022f.svg?style=flat-square)](https://insight.sensiolabs.com/projects/9bc06cdd-6bd0-43d5-b8fb-9768e174022f)
[![Quality Score](https://img.shields.io/scrutinizer/g/zupago/webpush.svg?style=flat-square)](https://scrutinizer-ci.com/g/zupago/webpush)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/zupago/webpush/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/zupago/webpush/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/zupago/webpush.svg?style=flat-square)](https://packagist.org/packages/zupago/webpush)

This package makes it easy to send web push notifications with Laravel.

## Installation

You can install the package via composer:

``` bash
composer require zupago/webpush
```

First you must install the service provider (skip for Laravel>=5.5):

``` php
// config/app.php
'providers' => [
    ...
    NotificationChannels\webpush\webpushServiceProvider::class,
],
```

Add the `NotificationChannels\webpush\HasPushSubscriptions` trait to your `User` model:

``` php
use NotificationChannels\webpush\HasPushSubscriptions;

class User extends Model
{
    use HasPushSubscriptions;
}
```

Next publish the migration with:

``` bash
php artisan vendor:publish --provider="NotificationChannels\webpush\webpushServiceProvider" --tag="migrations"
```

Run the migrate command to create the necessary table:

``` bash
php artisan migrate
```

You can also publish the config file with:

``` bash
php artisan vendor:publish --provider="NotificationChannels\webpush\webpushServiceProvider" --tag="config"
```

Generate the VAPID keys with (required for browser authentication) with:

``` bash
php artisan webpush:vapid
```

This command will set `VAPID_PUBLIC_KEY` and `VAPID_PRIVATE_KEY`in your `.env` file.

__These keys must be safely stored and should not change.__

If you still want support [Google Cloud Messaging](https://console.cloud.google.com) set the `GCM_KEY` and `GCM_SENDER_ID` in your `.env` file.

## Usage

Now you can use the channel in your `via()` method inside the notification as well as send a web push notification:

``` php
use Illuminate\Notifications\Notification;
use NotificationChannels\webpush\webpushMessage;
use NotificationChannels\webpush\webpushChannel;

class AccountApproved extends Notification
{
    public function via($notifiable)
    {
        return [webpushChannel::class];
    }

    public function towebpush($notifiable, $notification)
    {
        return webpushMessage::create()
            // ->id($notification->id)
            ->title('Approved!')
            ->icon('/approved-icon.png')
            ->body('Your account was approved!')
            ->action('View account', 'view_account');
    }
}
```

### Save/Update Subscriptions

To save or update a subscription use the `updatePushSubscription($endpoint, $key = null, $token = null)` method on your user:

``` php
$user = \App\User::find(1);

$user->updatePushSubscription($endpoint, $key, $token);
```

The `$key` and `$token` are optional and are used to encrypt your notifications. Only encrypted notifications can have a payload.

### Delete Subscriptions

To delete a subscription use the `deletePushSubscription($endpoint)` method on your user:

``` php
$user = \App\User::find(1);

$user->deletePushSubscription($endpoint);
```
## Browser Compatibility

The [Push API](https://developer.mozilla.org/en/docs/Web/API/Push_API) currently works on Chrome and Firefox.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```
