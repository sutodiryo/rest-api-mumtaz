<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function index()
    {
        $transaction = new TransactionService();

        return $transaction->lists();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:IN,OUT,EXPIRED,BROKEN,OTHERS',
            'customer_email' => 'nullable',
            'customer_name' => 'nullable',
            'total_amount' => 'nullable',
            'supplier_name' => 'nullable',
            'notes' => 'nullable',
            'items' => 'required',
        ]);

        $transaction = new TransactionService();

        return $transaction->createTransaction($validator);
    }

    public function show(Request $request)
    {
        $transaction = new TransactionService();

        return $transaction->showTransaction($request->transaction);
    }
}
