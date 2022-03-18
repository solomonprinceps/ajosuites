<?php

namespace App\Http\Controllers;

use App\Models\Saver;
use Illuminate\Http\Request;
use Seshac\Otp\Otp;
use Illuminate\Support\Facades\Auth;
use App\Events\SavercreationSms;
use App\Models\Moderator;
use Illuminate\Support\Str;

class SaverController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function SaverCreationStepOne(Request $request) {
        $user = Auth::user();
        // return $user;
        $request->validate([
            "phone_number" => "required|phone:NG"
        ]);
        $otp_engine = new Otp();
        $otp =  Otp::setValidity(10)->setLength(4)->setOnlyDigits(true)->setUseSameToken(false)->generate("$request->phone_number/$user->business_id");
        event(new SavercreationSms($otp->token, $this->unmake($request->phone_number)));
        return response([
            "message" => "OTP Sent to phone number",
            "status" => "success",
            "opt" => $otp
        ], 200);
    } 

    public static function unmake($phone){
        return str_replace("234", "0", $phone);
    }

    public function SaverCreationStepTwo(Request $request) {
        $user = Auth::user();
        $request->validate([
            "phone_number" => "required|phone:NG",
            "token" => "required|string"
        ]);
        $id = Auth::id();
        $moderator = Moderator::where("id", $id)->first();
        
        $verify = Otp::setAllowedAttempts(2)->validate("$request->phone_number/$user->business_id", $request->token);
        // return $verify;
        if ($verify->status == false) {
            return response([
                "status" => "error",
                "message" => "OTP does'nt exist"
            ], 400);
        }
        $newsaver = Saver::create([
            "business_id" => $moderator->business_id,
            "moderator_id" => $moderator->moderator_id,
            "status" => "0",
            "phone" => $request->phone_number,
            "saver_id" => "SAV".rand(100000000,999999999)
        ]);
        // return $moderator;
        return response([
            "status" => "success",
            "saver" => $newsaver,
            "message" => "Continue registration process"
        ], 200);
        
    }
    public function SaverCreationStepThree(Request $request) {
        $user = Auth::user();
        $request->validate([
            "email" => "required|email",
            "name" => "required|string",
            "saver_id" => "required|string"
        ]);
        $saver = Saver::where("saver_id", $request->saver_id)->first();
        if ($saver == null) {
            return response([
                "status" => "error",
                "message" => "Saver not available."
            ], 400);
        }
        if ($saver->status != "0") {
            return response([
                "status" => "error",
                "message" => "Not in this stage"
            ], 400);
        }
        $hashed_random_password = Str::str_random(8);

        $saver->update([
            "email" => $request->email,
            "password" => bcrypt($hashed_random_password),
            "password_string" => $hashed_random_password,
            "name" => $request->name,
            "status" => 1,
            "email_recievers" => json_encode([$request->email])
        ]);
        $saver->save();

        return response([
            "status" => "success",
            "saver" => $saver,
            "message" => "Saver created successfully."
        ], 200);
        
    }
}
