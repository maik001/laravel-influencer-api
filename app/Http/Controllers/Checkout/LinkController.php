<?php

namespace App\Http\Controllers\Checkout;

use App\Http\Resources\LinkResource;
use App\Models\Link;

class LinkController
{
    public function show ($code) {
        $link = Link::where('code', $code)->first();

        return new LinkResource($link);
    }
}
