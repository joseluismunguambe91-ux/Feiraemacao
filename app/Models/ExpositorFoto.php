<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpositorFoto extends Model
{
    protected $fillable = ['expositor_id', 'path', 'ordem'];

    public function expositor(): BelongsTo
    {
        return $this->belongsTo(Expositor::class);
    }
}
