<?php

namespace App\Models;

use App\Enums\AppointmentsStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointments extends Model
{
    use HasFactory;

    public function client(): BelongsTo {
        return $this->belongsTo(Clients::class);
    }

}
