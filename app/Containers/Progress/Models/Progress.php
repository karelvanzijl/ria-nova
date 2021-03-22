<?php

namespace App\Containers\Progress\Models;

use App\Containers\Trail\Models\Trail;
use App\Containers\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    use HasFactory;

    protected $table = 'trail_progresses';

    public function trail()
    {
        return $this->belongsTo(Trail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
