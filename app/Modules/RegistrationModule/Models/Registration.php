<?php

namespace App\Modules\RegistrationModule\Models;

use App\Models\Concerns\HasAudit;
use App\Models\User;
use App\Modules\RegistrationModule\Database\Factories\RegistrationFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Registration extends Model
{
    /** @use HasFactory<RegistrationFactory> */
    use HasAudit, HasFactory, HasUuids;

    protected $fillable = [
        'user_id', 'invoice_number', 'idempotency_key', 'total_rupiah',
        'status', 'confirmed_at', 'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'total_rupiah' => 'integer',
            'confirmed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function registrationTickets(): HasMany
    {
        return $this->hasMany(RegistrationTicket::class);
    }

    public function issuedTickets(): HasManyThrough
    {
        return $this->hasManyThrough(IssuedTicket::class, RegistrationTicket::class);
    }

    protected static function newFactory(): RegistrationFactory
    {
        return RegistrationFactory::new();
    }
}
