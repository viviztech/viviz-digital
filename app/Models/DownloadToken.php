<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class DownloadToken extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'order_id',
        'token',
        'expires_at',
        'download_count',
        'max_downloads',
        'ip_address',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'download_count' => 'integer',
            'max_downloads' => 'integer',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (DownloadToken $token) {
            if (empty($token->token)) {
                $token->token = Str::random(64);
            }

            if (empty($token->expires_at)) {
                $token->expires_at = now()->addHours(\App\Models\Setting::get('download_expiry_hours', 72));
            }
        });
    }

    /**
     * Get the order that owns the download token.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Check if the token is valid.
     */
    public function isValid(): bool
    {
        return $this->expires_at->isFuture()
            && $this->download_count < $this->max_downloads;
    }

    /**
     * Check if the token has expired.
     */
    public function hasExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if download limit is reached.
     */
    public function hasReachedLimit(): bool
    {
        return $this->download_count >= $this->max_downloads;
    }

    /**
     * Increment the download count.
     */
    public function incrementDownloadCount(): bool
    {
        return $this->increment('download_count');
    }

    /**
     * Generate a signed download URL.
     */
    public function generateSignedUrl(): string
    {
        return URL::temporarySignedRoute(
            'download.file',
            $this->expires_at,
            ['token' => $this->token]
        );
    }

    /**
     * Create a new download token for an order.
     */
    public static function createForOrder(Order $order, ?int $maxDownloads = null, ?int $expirationHours = null): static
    {
        $maxDownloads = $maxDownloads ?? \App\Models\Setting::get('max_downloads_per_order', 5);
        $expirationHours = $expirationHours ?? \App\Models\Setting::get('download_expiry_hours', 72);

        return static::create([
            'order_id' => $order->id,
            'max_downloads' => $maxDownloads,
            'expires_at' => now()->addHours($expirationHours),
        ]);
    }
}
