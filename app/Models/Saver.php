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
    protected $appends = ['emails'];
    protected $fillable = [
        "business_id",
        "moderator_id",
        "total_amount",
        "total_savings",
        "status",
        "phone",
        "name",
        "logo",
        "email",
        "password",
        "password_string",
        "address",
        "email_recievers",
        "saver_id"
    ];

    protected $hidden = [
        'password'
    ];

    public function getEmailsAttribute()
    {
        return json_decode($this->email_recievers);
    }

    public function savings()
    {
        return $this->hasMany(Savings::class, "saver_id", "saver_id");
    }
}
