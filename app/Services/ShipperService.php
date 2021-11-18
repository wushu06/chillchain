<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\Shipper;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class ShipperService implements ShipperInterface
{

    /**
     * @return LengthAwarePaginator
     */
    public function getData(): LengthAwarePaginator
    {
        return Shipper::latest()->paginate(self::page);
    }

    public function setData(Request $request, $shipper = null): ShipperService
    {
        $data = [
            'name' => $request->name,
            'address' => $request->address,
        ];

        if ($shipper) {
            $shipper->update($data);
            $this->detach($shipper);
        } else {
            $shipper = Shipper::create($data);
        }
        $contacts = Contact::find($request->contacts);
        if ($contacts) {
            $shipper->contacts()->saveMany($contacts);
        }
        if ($request->filled('primary_contact')) {
            $this->removePrimary($shipper)->addPrimary($shipper, $request->primary_contact);
        }
        return $this;
    }

    /**
     * @param $shipper
     * @return $this
     */
    public function detach($shipper): ShipperService
    {
        foreach ($shipper->contacts as $contact) {
            $contact->shipper_id = null;
            $contact->save();
        }
        return $this;
    }

    /**
     * @param $shipper
     * @param $primary_contact
     * @return $this
     */
    protected function addPrimary($shipper, $primary_contact): ShipperService
    {
        $contact = Contact::find($primary_contact);
        $contact->shipper_id = $shipper->id;
        $contact->is_primary = 1;
        $contact->save();
        return $this;
    }

    /**
     * @param $shipper
     * @return $this
     */
    protected function removePrimary($shipper): ShipperService
    {
        foreach ($shipper->contacts as $contact) {
            $contact->is_primary = 0;
            $contact->save();
        }
        return $this;
    }
}
