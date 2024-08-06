<?php

namespace App\Observers;

use App\Models\Address;

class AddressObserver
{
    /**
     * Listen to the Address creating event.
     */
    public function creating(Address $address): void
    {
        if (!$address->name) {
            $address->name = $address->town." ".$address->address_line_1." ".$address->address_line_2." ".$address->post_code;
        }
    }
}
