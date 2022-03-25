<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Savinglog extends Model
{
    use HasFactory;
    protected $table = "savinglogs";

    protected $fillable = [
        "savings_serial",
        "savinglog_id",
        "saver_id",
        "moderator_id",
        "business_id",
        "savings_type",
        "saving_amount",
        "expected_paid_date",
        "status"
    ];

    public function savings()
    {
        return $this->belongsTo(Savings::class, "savings_serial", "savings_serial");
    }
}
