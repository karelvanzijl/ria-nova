<?php

namespace App\Containers\Localization\Actions;

use Illuminate\Support\Facades\Http;

class GetAllLanguagesAction
{
    public function index()
    {
        $response = Http::withHeaders(['Accept' => 'application/json'])->get(env('RIA_API') . '/languages/all');

        $languages = [];
        foreach ($response->json() as $language) {
            $languages[$language['code']] = $language['default_name'];
        }

        return $languages;
    }
}
