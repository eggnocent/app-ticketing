<?php

namespace App\Modules\RegistrationModule\Database\Factories;

use App\Models\User;
use App\Modules\RegistrationModule\Models\Registration;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Registration>
 */
class RegistrationFactory extends Factory
{
    protected $model = Registration::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'invoice_number' => 'INV-'.Str::upper(Str::random(12)),
            'idempotency_key' => null,
            'total_rupiah' => 100000,
            'status' => 'completed',
            'confirmed_at' => now(),
            'cancelled_at' => null,
        ];
    }
}
