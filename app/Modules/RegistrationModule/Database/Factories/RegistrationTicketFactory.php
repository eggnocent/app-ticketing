<?php

namespace App\Modules\RegistrationModule\Database\Factories;

use App\Modules\RegistrationModule\Models\Registration;
use App\Modules\RegistrationModule\Models\RegistrationTicket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RegistrationTicket>
 */
class RegistrationTicketFactory extends Factory
{
    protected $model = RegistrationTicket::class;

    public function definition(): array
    {
        return [
            'registration_id' => Registration::factory(),
            'ticket_id' => null,
            'quantity' => 1,
            'unit_price_rupiah' => 100000,
            'subtotal_rupiah' => 100000,
        ];
    }
}
