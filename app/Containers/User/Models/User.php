<?php

namespace App\Containers\User\Models;

use App\Containers\Customer\Models\Customer;
use App\Containers\Localization\Actions\GetAllCountriesAction;
use App\Containers\Localization\Actions\GetAllLanguagesAction;
use App\Containers\Role\Models\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements HasMedia
{
    use HasFactory, Notifiable, InteractsWithMedia, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function __construct()
    {
        if (!Cache::store('redis')->has('ria_countries')) {
            Cache::store('redis')->put('ria_countries', (new GetAllCountriesAction())->index(), 3600);
            Cache::store('redis')->put('ria_languages', (new GetAllLanguagesAction())->index(), 3600);
        }
        config([
            'ria_countries' => Cache::get('ria_countries'),
            'ria_languages' => Cache::get('ria_languages'),
        ]);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')->singleFile();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function roles()
    {
        return $this->morphToMany(Role::class, 'model', 'model_has_roles');
    }

    public function highestRole()
    {
        return $this->morphToMany(Role::class, 'model', 'model_has_roles')->orderBy('roles.level', 'desc');
    }

    public function getNameAttribute()
    {
        return $this->firstname . ' ' . $this->lastname;
    }
}
