<?php

namespace App\Modules\TicketModule\Models;

use App\Models\Concerns\HasAudit;
use App\Modules\TicketModule\Database\Factories\TicketFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    /** @use HasFactory<TicketFactory> */
    use HasAudit, HasFactory, HasUuids;

    protected $fillable = [
        'event_id', 'name', 'price_rupiah', 'initial_quota',
        'remaining_quota', 'sales_start', 'sales_end', 'status',
    ];

    protected function casts(): array
    {
        return [
            'price_rupiah' => 'integer',
            'initial_quota' => 'integer',
            'remaining_quota' => 'integer',
            'sales_start' => 'datetime',
            'sales_end' => 'datetime',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function isOnSale(): bool
    {
        return $this->status === 'active'
            && $this->sales_start <= now()
            && $this->sales_end >= now();
    }

    protected static function newFactory(): TicketFactory
    {
        return TicketFactory::new();
    }
}
