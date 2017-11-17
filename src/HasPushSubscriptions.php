<?php

namespace NotificationChannels\WebPush;
//use Emadadly\LaravelUuid\Uuids;

trait HasPushSubscriptions
{
    // use Uuids;
    //     public $incrementing = false;
    /**
     * Get the user's subscriptions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pushSubscriptions()
    {
        return $this->hasMany(PushSubscription::class);
    }

    /**
     * Update (or create) user subscription.
     *
     * @param  string $endpoint
     * @param  string|null $key
     * @param  string|null $token
     * @return \NotificationChannels\WebPush\PushSubscription
     */
    public function updatePushSubscription($endpoint, $key = null, $token = null, $dd = null, $push_subscription_id = null, $name = null, $language = null, $device = null, $country = null)

    {
        $subscription = PushSubscription::findByEndpoint($endpoint);
        if ($subscription && $this->pushSubscriptionBelongsToUser($subscription)) {
            $subscription->public_key = $key;
            $subscription->auth_token = $token;
            $subscription->browser = $dd;
            $subscription->push_subscription_id = $push_subscription_id ;
            $subscription->name = $name;
            $subscription->language = $language;
            $subscription->device = $device;
            $subscription->country = $country;
            $subscription->save();

            return $subscription;
        }

        if ($subscription && ! $this->pushSubscriptionBelongsToUser($subscription)) {
            $subscription->delete();
        }

        return $this->pushSubscriptions()->save(new PushSubscription([
            'endpoint' => $endpoint,
            'public_key' => $key,
            'auth_token' => $token,
        ]));
    }

    /**
     * Determine if the given subscription belongs to this user.
     *
     * @param  \NotificationChannels\WebPush\PushSubscription $subscription
     * @return bool
     */
    public function pushSubscriptionBelongsToUser($subscription)
    {
        return (int) $subscription->user_id === 1;
    }

    /**
     * Delete subscription by endpoint.
     *
     * @param  string $endpoint
     * @return void
     */
    public function deletePushSubscription($endpoint)
    {
        $this->pushSubscriptions()
            ->where('endpoint', $endpoint)
            ->delete();
    }

    /**
     * Get all subscriptions.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function routeNotificationForWebPush()
    {
        return $this->pushSubscriptions;
    }
}
