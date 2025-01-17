<?php

namespace RecursiveTree\Seat\TreeLib\Prices;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Seat\Eveapi\Models\Market\Price;


class CCPPricesPriceProvider extends AbstractPriceProvider
{

    public static function getPrices($items, $settings)
    {
        return $items->map(function ($item){
            $price = Price::find($item->typeModel->typeID)->adjusted_price ?? $item->typeModel->basePrice ?? 0;
            if($item->price == null) {
                $item->price = $price;
            }
            $item->marketPrice = $price;
            return $item;
        });
    }
}