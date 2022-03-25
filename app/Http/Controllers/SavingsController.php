<?php

namespace App\Http\Controllers;

use App\Models\Savings;
use Illuminate\Http\Request;
use App\Models\Saver;
use Illuminate\Support\Facades\Auth;
use App\Models\Savinglog;
use App\Models\Business;
use App\Events\SavingsCreationProcessed;
use Propaganistas\LaravelPhone\PhoneNumber;

class SavingsController extends Controller
{
    public function listsavings(Request $request) {
        $moderator = Auth::user();
        $request->validate([
            "saver_id" => "required|string",
        ]);
        $user = Auth::user();
        $saver = Saver::where("saver_id", $request->saver_id)->where("business_id", $moderator->business_id)->first();
        if($saver == null) {
            return response([
                "status" => "error",
                "message" => "Saver not available"
            ], 400);    
        }
        $savings = Savings::where("saver_id", $request->saver_id)->where("business_id", $user->business_id)->orderBy('status', 'asc')->get();
        return response([
            "status" => "success",
            "savings" => $savings,
            "saver" => $saver,
            "message" => "Savings Fetched successfully"
        ], 200);
        
    }

    public static function make($phone, $country = "NG")
    {
        $phoneNumber = PhoneNumber::make($phone, $country);
        return $phoneNumber;
    }
    
    public function createsavingsdaily(Request $request)
    {
        $request->validate([
            "saver_id" => "required|string",
            "savings_type" => "required|string",
            "saving_amount" => "required|string",
            "duration_list" => "required|string",
        ]);
        $user = Auth::user();
        $dura = json_decode($request->duration_list);
        if ($request->saving_amount < 500) {
            return response([
                "status" => "error",
                "message" => "Amount should be more than 500 naira"
            ], 400);
        }
        $saver = Saver::where("saver_id", $request->saver_id)->where("business_id", $user->business_id)->first();
        if ($saver == null) {
            return response([
                "status" => "error",
                "message" => "Saver doesn't exist"
            ], 400);
        }
        $savings_serial = "SA".rand(100000,999999).rand(100000,999999);
        
        $business = Business::where("business_id", $saver->business_id)->first();
        if ($business == null) {
            return response([
                "message" => "Business doesn't exist",
                "status" => "error"
            ], 400);
        }
        if ($business->balance < 100) {
            return response([
                "message" => "Business account balance is low",
                "status" => "error"
            ], 400);
        }
        $business->withdraw(100);

        $createSavings = Savings::create([
            "saver_id" => $saver->saver_id,
            "savings_serial" => $savings_serial,
            "savings_type" => "Daily",
            "saving_amount" => $request->saving_amount,
            "saving_total_amount" => $request->saving_amount * count($dura),
            "saving_interval" => count($dura),
            "moderator_id" => $user->moderator_id,
            "business_id" => $user->business_id,
            "start_date" => $dura[0],
            "end_date" => $dura[count($dura)-1]
        ]);

        foreach ($dura as $value) {
            Savinglog::create([
                "savings_serial" => $savings_serial,
                "savinglog_id" => "SL".rand(100000000,999999999),
                "saver_id" => $saver->saver_id,
                "moderator_id" => $user->moderator_id,
                "business_id" => $user->business_id,
                "savings_type" => "Daily",
                "expected_paid_date" => $value,
                "saving_amount" => $request->saving_amount,
            ]);
        }
        $createSavings->savingslog;
        $createSavings->saver;
        $saver->update([
            "total_savings" => (int)$saver->total_savings + 1,
        ]);
        $saver->save();
        event(new SavingsCreationProcessed(ucwords($saver->name)." Your Ajosuite saving $savings_serial of amount NGN".number_format($createSavings->saving_amount)." for $createSavings->saving_interval interval has been created succcessfully.", $this->make($saver->phone)));
        return response([
            "status" => "success",
            "message" => "Daily Savings Created.",
            "saver" => $saver,
            "savings" => $createSavings
        ], 200);
    }

}
