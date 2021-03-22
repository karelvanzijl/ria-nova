<?php

namespace App\Containers\Customer\Models;

use App\Containers\Trail\Models\Trail;
use App\Containers\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Customer extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')->singleFile();
        $this->addMediaCollection('logo_large')->singleFile();
        $this->addMediaCollection('logo_small')->singleFile();
        $this->addMediaCollection('cover')->singleFile();
    }

    public function root()
    {
        return $this->belongsTo(User::class, 'user_id')->where('users.customer_id', $this->id);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function trails()
    {
        return $this->hasMany(Trail::class);
    }
}
