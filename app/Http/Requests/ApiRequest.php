<?php

namespace App\Http\Requests;

use App;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Arr;
use Response;

class ApiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    protected function failedValidation(Validator $validator)
    {
        $messages = current(Arr::first($validator->errors()->toArray()));

        $response = Response::json($this->makeError($messages), HttpResponse::HTTP_BAD_REQUEST);

        throw new HttpResponseException($response);
    }

    private function makeError(string $message): array
    {
        $res = [
            'success' => false,
            'message' => $message,
        ];

        return $res;
    }
}
