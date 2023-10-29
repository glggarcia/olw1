<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PunkapiService
{
    /**
        * @param string|null $beerName
        * @param string|null $food
        * @param string|null $malt
        * @param int|null $ibuGt
        * @return Http|array
     */
    public function getBeers(
        ?string $beerName = null,
        ?string $food = null,
        ?string $malt = null,
        ?int $ibuGt = null
    ): Http|array
    {
        $queryParams = [
            'beer_name' => $beerName,
            'food' => $food,
            'malt' => $malt,
            'ibu_gt' => $ibuGt
        ];

        if (Cache::has($beerName)) {
            return Cache::get($beerName);
        }

        $beers =  Http::punkapi()
            ->get('/beers', $queryParams)
            ->throw()
            ->json();

        Cache::put($beerName, $beers, now()->addMinutes(10));

        return $beers;
    }
}
