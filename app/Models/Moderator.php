<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Moderator extends Authenticatable
{
    
    use HasApiTokens, HasFactory, Notifiable,HasRoles;

    protected $table = "moderators";
    protected $fillable = [
        "business_id",
        "moderator_id",
        "password",
        "transaction_pin",
        "type",
        "logo",
        "phone",
        "name",
        "email"
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function business() {
        return $this->hasMany(Business::class, "business_id", "business_id");
    }


}
