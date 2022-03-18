<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Moderator;
use Bavix\Wallet\Traits\HasWallet;
use Bavix\Wallet\Interfaces\Wallet;


class Business extends Model implements Wallet
{
    use HasFactory;
    use HasWallet;
    

    protected $table = "businesses";
    protected $fillable = [
        "business_name",
        "logo",
        "business_description",
        "admin_id",
        "status",
        "business_id"
    ];
    protected $appends = array('balance');

    public function moderators() {
        return $this->hasMany(Moderator::class, "business_id", "business_id");
    }


    protected $hidden = [
        'wallet',
        // 'remember_token',
    ];
}
