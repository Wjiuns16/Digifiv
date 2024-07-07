<?php

namespace App\Http\Requests;

use App\Models\Event;

class CreateEventRequest extends ApiRequest
{
    public function rules()
    {
        return Event::$rules;
    }
}
