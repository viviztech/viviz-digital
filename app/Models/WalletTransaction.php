<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'type',
        'amount',
        'opening_balance',
        'closing_balance',
        'reference_id',
        'description',
        'status',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
