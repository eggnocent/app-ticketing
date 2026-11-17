<?php

namespace App\Modules\TicketModule\Exceptions;

use Illuminate\Http\Request;
use RuntimeException;

class TicketQuotaExceededException extends RuntimeException
{
    public function render(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $this->getMessage()], 422);
        }

        return back()->withInput()->with('error', $this->getMessage());
    }
}
