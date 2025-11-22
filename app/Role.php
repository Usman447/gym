<?php

namespace App;

use Auth;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';

    protected $fillable = [
            'name',
            'display_name',
            'description',
    ];

    public function users()
    {
        return $this->belongsToMany('App\User', 'role_user', 'role_id', 'user_id');
    }

    public function permissions()
    {
        return $this->belongsToMany('App\Permission', 'permission_role', 'role_id', 'permission_id');
    }

    public function roleUsers()
    {
        return $this->belongsToMany('App\RoleUser');
    }

    public function scopeExcludeGymie($query)
    {
        if (Auth::User()->id != 1) {
            return $query->where('id', '!=', 1);
        }

        return $query;
    }
}
