<?php

namespace RecursiveTree\Seat\TreeLib\Parser;

use RecursiveTree\Seat\TreeLib\Items\EveItem;
use Seat\Eveapi\Models\Sde\InvType;

class FitParser extends Parser
{
    protected static function parse($fit)
    {
        $items = [];

        //parse ship type
        $matches = [];
        $res = preg_match("/\[([^,]+),[^,]+]/",$fit,$matches);
        if($res!=1) {
            return null;
        }
        $inv_model = InvType::where('typeName', $matches[1])->first();
        $ship = new EveItem($inv_model);
        $ship->amount = 1;
        array_push($items,$ship);

        //parse ship name
        $matches = [];
        $res = preg_match("/\[[^,]+,([^,]+)]/",$fit, $matches);
        if($res!=1) {
            return null;
        }
        $name = $matches[1];

        //parse fit body
        $matches=[];
        preg_match_all('/^(?<names>[[:alnum:]\' \-]+?)(?:, [[:alnum:]\' \-]+?)?(?: x(?<amounts>\d+))?$/mu', $fit, $matches);
        $names = $matches["names"];
        $amounts = $matches["amounts"];
        for ($i=0;$i<count($names);$i++){
            $item_name = $names[$i];
            $amount = intval($amounts[$i]);

            $inv_model = InvType::where('typeName', $item_name)->first();

            if($inv_model==null){
                continue;
            }

            $item = new EveItem($inv_model);
            $item->amount = $amount>0 ? $amount:1;

            array_push($items,$item);
        }

        $result = new ParseResult(collect($items));
        $result->ship = $ship;
        $result->shipName = $name;
        return $result;
    }
}