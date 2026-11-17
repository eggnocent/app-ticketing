<?php

namespace App\Modules\RegistrationModule\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\RegistrationModule\Models\Registration;
use Illuminate\View\View;

class AdminRegistrationController extends Controller
{
    public function index(): View
    {
        $registrations = Registration::query()
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(15);

        $completed = Registration::query()->where('status', 'completed');

        return view('registration::admin.index', [
            'registrations' => $registrations,
            'totalRegistrations' => $completed->count(),
            'totalRevenue' => (int) $completed->sum('total_rupiah'),
        ]);
    }
}
