<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsInboundAttributes;
use Illuminate\Support\Facades\Hash as HashFacade;

class Hash implements CastsInboundAttributes
{
    public function set($model, $key, $value, $attributes)
    {
        return HashFacade::driver($attributes[0]??null)->make($value, array_slice($attributes, 1));
    }
}
