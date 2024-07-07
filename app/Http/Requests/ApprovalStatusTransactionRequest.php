<?php

namespace App\Http\Requests;

use App\Models\Transaction;

class ApprovalStatusTransactionRequest extends ApiRequest
{
    public function rules()
    {
        return Transaction::$rules;
    }
}
