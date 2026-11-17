<?php

namespace App\Modules\EventModule\Models;

use App\Models\Concerns\HasAudit;
use App\Modules\EventModule\Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    /** @use HasFactory<TagFactory> */
    use HasAudit, HasFactory, HasUuids;

    protected $fillable = ['name', 'slug'];

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_tag')
            ->using(EventTag::class)
            ->withPivot(['id', 'created_by', 'updated_by'])
            ->withTimestamps();
    }

    protected static function newFactory(): TagFactory
    {
        return TagFactory::new();
    }
}
