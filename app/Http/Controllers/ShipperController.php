<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShipperRequest;
use App\Models\Contact;
use App\Models\Shipper;
use App\Services\ShipperInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShipperController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return LengthAwarePaginator
     */
    public function index(ShipperInterface $service)
    {
        return $service->getData();
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param ShipperRequest $request
     * @param ShipperInterface $service
     */
    public function store(ShipperRequest $request, ShipperInterface $service)
    {
        $request->validated();
        return $service->setData($request)->getData();
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $shipper = Shipper::find($id);
        return $shipper->contacts;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return LengthAwarePaginator
     */
    public function update(Request $request,  ShipperInterface $service,  $id)
    {
        $shipper = Shipper::find($id);
        return $service->setData($request, $shipper)->getData();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @param ShipperInterface $service
     * @return LengthAwarePaginator|null
     */
    public function destroy($id, ShipperInterface $service)
    {
        $shipper = Shipper::find($id);
        $service->detach($shipper);
        return Shipper::destroy($id) ? $service->getData() : null;
    }

    /**
     * Search for a name
     *
     * @param string $name
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function search(string $name): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return DB::table('shippers')
            ->where('name', 'like', '%' . $name . '%')
            ->orWhere('address', 'like', '%' . $name . '%')
            ->paginate(ShipperInterface::page);
    }
}
