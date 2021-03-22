<?php

namespace App\Containers\Trail\Models;

use App\Containers\Customer\Models\Customer;
use App\Containers\Progress\Models\Progress;
use App\Containers\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Trail extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('icon')->singleFile();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function progresses()
    {
        return $this->hasMany(Progress::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'trail_progresses');
    }
}
