<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function joinUserManagementRole()
    {
        return DB::table('users as A')
            ->select(
                'A.id',
                'A.name',
                'A.username',
                'A.email',
                'C.name as role_name',
                'C.description as role_description',
                'C.slug as role_slug',
                'E.name as permission_name',
                'E.slug as permission_slug',
                'E.description as permission_description'
            )
            ->join('role_user as B', 'A.id', '=', 'B.user_id')
            ->join('role as C', 'C.id', '=', 'B.role_id')
            ->join('permissions_role as D', 'D.role_id', '=', 'C.id')
            ->join('permissions as E', 'E.id', '=', 'D.permission_id')
            ->join('permissions_user as F', function ($join) {
                $join->on('F.permission_id', '=', 'E.id')
                    ->on('F.user_id', '=', 'A.id');
            })
            ->get();
    }
}
