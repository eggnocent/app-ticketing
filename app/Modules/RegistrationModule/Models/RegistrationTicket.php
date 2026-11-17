<?php

namespace App\Modules\RegistrationModule\Models;

use App\Models\Concerns\HasAudit;
use App\Modules\RegistrationModule\Database\Factories\RegistrationTicketFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RegistrationTicket extends Model
{
    /** @use HasFactory<RegistrationTicketFactory> */
    use HasAudit, HasFactory, HasUuids;

    protected $fillable = [
        'registration_id', 'ticket_id', 'quantity',
        'unit_price_rupiah', 'subtotal_rupiah',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price_rupiah' => 'integer',
            'subtotal_rupiah' => 'integer',
        ];
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function issuedTickets(): HasMany
    {
        return $this->hasMany(IssuedTicket::class);
    }

    protected static function newFactory(): RegistrationTicketFactory
    {
        return RegistrationTicketFactory::new();
    }
}
