<?php

namespace App\Services;

use Illuminate\Http\Request;

interface ShipperInterface
{
    public const page = 10;
    public function getData();
    public function setData(Request $request, $shipper = null);
    public function detach($shipper);
}
