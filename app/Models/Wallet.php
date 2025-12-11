<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
        'currency',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class)->latest();
    }

    // Helper to credit amounts safely
    public function credit($amount, $description, $referenceId = null, $status = 'success')
    {
        return DB::transaction(function () use ($amount, $description, $referenceId, $status) {
            $openingBalance = $this->balance;

            // Update balance
            $this->increment('balance', $amount);

            // Refresh to get new balance
            $this->refresh();

            // Log transaction
            return $this->transactions()->create([
                'type' => 'credit',
                'amount' => $amount,
                'opening_balance' => $openingBalance,
                'closing_balance' => $this->balance,
                'description' => $description,
                'reference_id' => $referenceId,
                'status' => $status,
            ]);
        });
    }

    // Helper to debit amounts safely
    public function debit($amount, $description, $referenceId = null)
    {
        return DB::transaction(function () use ($amount, $description, $referenceId) {
            if ($this->balance < $amount) {
                throw new \Exception("Insufficient wallet balance.");
            }

            $openingBalance = $this->balance;

            // Update balance
            $this->decrement('balance', $amount);

            // Refresh
            $this->refresh();

            // Log transaction
            return $this->transactions()->create([
                'type' => 'debit',
                'amount' => $amount,
                'opening_balance' => $openingBalance,
                'closing_balance' => $this->balance,
                'description' => $description,
                'reference_id' => $referenceId,
                'status' => 'success',
            ]);
        });
    }
}
