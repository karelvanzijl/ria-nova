<?php

namespace App\Containers\Localization\Actions;

use Illuminate\Support\Facades\Http;

class GetAllCountriesAction
{
    public function index()
    {
        $response = Http::withHeaders(['Accept' => 'application/json'])->get(env('RIA_API') . '/countries/all');

        $countries = [];
        foreach ($response->json() as $country) {
            $countries[$country['code']] = $country['default_name'];
        }

        return $countries;
    }
}
