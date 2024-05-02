<?php
/**
 * This file is published from the "faithfm/laravel-simple-permissions" composer package.
 *    WARNING: Local modifications will be overwritten if the package is published during updates.
 *             See https://github.com/faithfm/laravel-simple-permissions for more details.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'permission', 'restrictions',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'restrictions' => 'array',
    ];

    /**
     * Get the user that owns the permissions.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
