<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class Saver extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = "savers";
    protected $fillable = [
        "business_id",
        "moderator_id",
        "total_amount",
        "status",
        "phone",
        "name",
        "logo",
        "email",
        "password",
        "password_string",
        "email_recievers",
        "saver_id"
    ];

    protected $hidden = [
        'password'
    ];
}
