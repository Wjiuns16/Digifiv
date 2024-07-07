<?php

namespace App\Http\Requests;

use App\Models\Event;

class EditEventRequest extends ApiRequest
{
    public function rules()
    {
        return array_merge(Event::$rules, [
            'available_tickets' => 'bail|required|numeric|min:1',
        ]);
    }
}
