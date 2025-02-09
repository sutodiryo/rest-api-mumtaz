<?php

namespace App\Services;

use App\Events\SuccessfulTransaction;
use App\Http\Resources\TransactionResource;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionService
{

    public function lists()
    {
        $posts = Transaction::latest()->paginate(1);

        return new TransactionResource(true, 'success', $posts);
    }

    public function createTransaction($request)
    {
        if ($request->fails()) {

            return new TransactionResource(false, $request->errors(), null);
        } else {

            DB::beginTransaction();

            try {
                $transaction = Transaction::create([
                    'type' => $request->validated()['type'],
                    'customer_email' => $request->validated()['customer_email'],
                    'customer_name' => $request->validated()['customer_name'],
                    'supplier_name'   => $request->validated()['supplier_name'],
                    'notes'   => $request->validated()['notes'],
                    'created_by_id'   => Auth::id(),
                ]);

                $items = $request->validated()['items'];

                $total_amounts = [];

                foreach ($items as $key => $item) {

                    $quantity = $item['quantity'];

                    $product = Product::findOrFail($item['product_id']);

                    $total_amount = $quantity * $product->price;
                    $total_amounts[] = $total_amount;
                    $quantity_after = $product->quantity - $quantity;

                    $createdItem = TransactionItem::create([
                        'quantity' => $quantity,
                        'total_amount' => $total_amount,
                        'quantity_before' => $product->quantity,
                        'quantity_after' => $quantity_after,
                        'product_id' => $product->id,
                        'transaction_id' => $transaction->id,
                        'created_by_id'   => Auth::id(),
                    ]);

                    $product->quantity = $quantity_after;
                    $product->save();
                }

                $transaction->total_amount = array_sum($total_amounts);
                $transaction->save();

                event(new SuccessfulTransaction($transaction));

                return new TransactionResource(true, 'Transaction created!', $transaction);

                DB::commit();
            } catch (\Throwable $th) {
                DB::rollback();
                return new TransactionResource(false, $th(), null);
            }
        }
    }

    public function showTransaction($id)
    {
        $transaction = Transaction::with('items')->where('id', $id)->first();

        if (!$transaction) {

            return new TransactionResource(false, 'Transaction not found', null);
        } else {

            return new TransactionResource(true, 'Transaction find succesfully!', $transaction);
        }
    }
}
