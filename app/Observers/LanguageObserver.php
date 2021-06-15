<?php

namespace App\Observers;

use App\Containers\Localization\Models\Language;
use Illuminate\Support\Facades\Http;

class LanguageObserver
{
    public function updated(Language $language)
    {
        if ($language->active === true) {
            $url = env('RIA_API') . '/localizations/office';
            Http::post($url, [
                'iso' => $language->lang_iso,
            ]);
        }
    }
}
