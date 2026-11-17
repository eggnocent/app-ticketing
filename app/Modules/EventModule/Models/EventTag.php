<?php

namespace App\Modules\EventModule\Models;

use App\Models\Concerns\HasAudit;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\Pivot;

class EventTag extends Pivot
{
    use HasAudit, HasUuids;

    protected $table = 'event_tag';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['event_id', 'tag_id'];
}
