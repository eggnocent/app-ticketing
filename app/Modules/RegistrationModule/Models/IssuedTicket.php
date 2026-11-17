<?php

namespace App\Modules\RegistrationModule\Models;

use App\Models\Concerns\HasAudit;
use App\Modules\RegistrationModule\Database\Factories\IssuedTicketFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IssuedTicket extends Model
{
    /** @use HasFactory<IssuedTicketFactory> */
    use HasAudit, HasFactory, HasUuids;

    protected $fillable = [
        'registration_ticket_id', 'ticket_code', 'status', 'checked_in_at',
    ];

    protected function casts(): array
    {
        return [
            'checked_in_at' => 'datetime',
        ];
    }

    public function registrationTicket(): BelongsTo
    {
        return $this->belongsTo(RegistrationTicket::class);
    }

    protected static function newFactory(): IssuedTicketFactory
    {
        return IssuedTicketFactory::new();
    }
}
