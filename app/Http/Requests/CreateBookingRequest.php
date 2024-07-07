<?php

namespace App\Http\Requests;

use App\Models\Booking;

class CreateBookingRequest extends ApiRequest
{
    public function rules()
    {
        return Booking::$rules;
    }
}
