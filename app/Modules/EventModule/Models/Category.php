<?php

namespace App\Modules\EventModule\Models;

use App\Models\Concerns\HasAudit;
use App\Modules\EventModule\Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasAudit, HasFactory, HasUuids;

    protected $fillable = ['name', 'slug', 'description'];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    protected static function newFactory(): CategoryFactory
    {
        return CategoryFactory::new();
    }
}
