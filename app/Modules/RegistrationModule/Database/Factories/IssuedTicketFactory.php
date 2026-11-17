<?php

namespace App\Modules\RegistrationModule\Database\Factories;

use App\Modules\RegistrationModule\Models\IssuedTicket;
use App\Modules\RegistrationModule\Models\RegistrationTicket;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<IssuedTicket>
 */
class IssuedTicketFactory extends Factory
{
    protected $model = IssuedTicket::class;

    public function definition(): array
    {
        return [
            'registration_ticket_id' => RegistrationTicket::factory(),
            'ticket_code' => 'TKT-'.Str::upper(Str::random(12)),
            'status' => 'valid',
            'checked_in_at' => null,
        ];
    }
}
