<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApprovalStatusTransactionRequest;
use App\Models\Transaction;
use Exception;

class TransactionController extends Controller
{
    public function approvalStatus(ApprovalStatusTransactionRequest $request, $id)
    {
        try {
            $transaction = $this->checkTransactionId($id);
            if (empty($transaction)) {
                return $this->sendError(null, 'Transaction not found');
            }

            if ($transaction->status != Transaction::STATUS_PENDING) {
                return $this->sendError(null, 'Transaction is success/failed');
            }

            $message = '';
            switch ($request->status) {
                case Transaction::STATUS_SUCCESS:
                    $transaction->markAsSuccess();
                    $message = "Transaction has marked as success";
                    break;
                case Transaction::STATUS_FAILED:
                    $transaction->markAsFailed();
                    $message = "Transaction has marked as failed";
                    break;
            }

            return $this->sendResponse(null, $message);
        } catch (Exception $e) {
            return $this->sendError(null, $e->getMessage());
        }
    }

    private function checkTransactionId($id)
    {
        if (!empty(intval($id))) {
            return Transaction::where('id', $id)->first();
        }

        return null;
    }
}
