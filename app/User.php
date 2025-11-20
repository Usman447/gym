<?php

namespace App;

use Auth;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
// use Zizaco\Entrust\Traits\EntrustUserTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;

class User extends Authenticatable implements HasMedia
{
    use Notifiable, HasMediaTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mst_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password', 'status', 'timings'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Register media conversions
     *
     * @param Media|null $media
     * @return void
     */
    public function registerMediaConversions(Media $media = null)
    {
        $this->addMediaConversion('thumb')
             ->width(50)
             ->height(50)
             ->quality(100)
             ->fit('crop', 50, 50)
             ->performOnCollections('staff');

        $this->addMediaConversion('form')
             ->width(70)
             ->height(70)
             ->quality(100)
             ->fit('crop', 70, 70)
             ->performOnCollections('staff');
    }

    /**
     * Scope to exclude archived users
     */
    public function scopeExcludeArchive($query)
    {
        if (Auth::check() && Auth::user()->id != 1) {
            return $query->where('status', '!=', \constStatus::Archive)->where('id', '!=', 1);
        }

        return $query->where('status', '!=', \constStatus::Archive);
    }

    /**
     * Get role user relationship
     */
    public function roleUser()
    {
        return $this->hasOne('App\RoleUser');
    }
}
