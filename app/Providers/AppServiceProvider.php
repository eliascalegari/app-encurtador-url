<?php

namespace App\Providers;

use App\Models\Url;
use App\Services\UrlCache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Url::created(function () {
            Cache::forget('urls');
        });

        Url::deleted(function () {
            Cache::forget('urls');
        });
    }

    public function register()
    {
        //
    }
}
