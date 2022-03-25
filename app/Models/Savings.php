<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Savinglog;

class Savings extends Model
{
    use HasFactory;
    protected $table = "savings";

    protected $fillable = [
        "saver_id",
        "savings_serial",
        "savings_type",
        "moderator_id",
        "business_id",
        "saving_amount",
        "saving_total_amount",
        "saving_interval",
        "start_date",
        "end_date"
    ];

    public function savingslog()
    {
        return $this->hasMany(Savinglog::class, "savings_serial", "savings_serial");
    }

    public function saver()
    {
        return $this->belongsTo(saver::class, "saver_id", "saver_id");
    }
}
