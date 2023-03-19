<?php

namespace App\Services;

use App\Models\Url;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;


class UrlCache
{
    public static function getAll()
    {
        $urls = Cache::remember('urls', 60 * 10, function () {
            $ten_hours_ago = Carbon::now()->subHours(10);
            return Url::where('created_at', '>=', $ten_hours_ago)->get();
        });

        return $urls;
    }

    public static function getById($id)
    {
        $url = Cache::get("url_{$id}");

        if ($url === null) {
            try {
                $url = Url::findOrFail($id);
                Cache::put("url_{$id}", $url, 60 * 10);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
                // Tratar o erro aqui
            }
        }

        return $url;
    }

    public static function invalidateCache()
    {
        Cache::forget('urls');
    }
}
