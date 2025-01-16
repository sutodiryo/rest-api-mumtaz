<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    use HasUuids;

    protected $fillable = [
        'quantity',
        'total_amount',
        'quantity_before',
        'quantity_after',
        'product_id',
        'transaction_id',
        'created_by_id',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
