<?php

namespace App;

class Util
{
    public static function isProduction()
    {
        return env('APP_ENV') == 'production';
    }

    public static function appUrl()
    {
        return env('APP_URL');
    }

    public static function appName()
    {
        return env('APP_NAME');
    }

    public static function twitterUsername()
    {
        return env('TWITTER_USERNAME');
    }

    public static function lastTweetId()
    {
        return env('TWITTER_LAST_TWEET_ID');
    }

    public static function svgLogo()
    {
        return env('LOGO_SVG');
    }

    public static function googleGeocodingApiKey()
    {
        return env('GOOGLE_GEOCODING_API');
    }

    /**
     * @param $user User
     *
     * @return bool
     */
    public static function isAdmin($user)
    {
        if (! $user) {
            return false;
        }

        $adminEmails = explode(',', env('ADMIN_EMAILS'));
        return (in_array($user->email(), $adminEmails));
    }

    public static function airtableUrl()
    {
        return "https://airtable.com/" . env('AIRTABLE_BASE_ID');
    }

    public static function algoliaAppId()
    {
        return env('ALGOLIA_APP_ID');
    }

    public static function algoliaPublicKey()
    {
        return env('ALGOLIA_PUBLIC_KEY');
    }

    /**
     * @param $user User
     *
     * @return mixed
     */
    public static function algoliaPublicKeyFor($user = null)
    {
        if (isset($user) && $user && $user->isAdmin()) {
            return env('ALGOLIA_PUBLIC_KEY_ADMIN');
        }

        return env('ALGOLIA_PUBLIC_KEY');
    }

    public static function algoliaPrivateKey()
    {
        return env('ALGOLIA_PRIVATE_KEY');
    }
}