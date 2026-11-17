<?php

namespace App\Modules\RegistrationModule\Exceptions;

use Illuminate\Http\Request;
use RuntimeException;

class CheckoutFailedException extends RuntimeException
{
    public function render(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $this->getMessage()], 422);
        }

        return back()->withInput()->with('error', $this->getMessage());
    }
}
