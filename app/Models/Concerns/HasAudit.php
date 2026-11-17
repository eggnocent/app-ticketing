<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait HasAudit
{
    public static function bootHasAudit(): void
    {
        static::creating(function (Model $model): void {
            $actor = Auth::id();

            $model->created_by ??= $actor;
            $model->updated_by ??= $actor;
        });

        static::updating(function (Model $model): void {
            $model->updated_by = Auth::id();
        });
    }
}
