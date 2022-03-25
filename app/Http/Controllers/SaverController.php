<?php

namespace App\Http\Controllers;

use App\Models\Saver;
use Illuminate\Http\Request;
use Seshac\Otp\Otp;
use Illuminate\Support\Facades\Auth;
use App\Models\Business;
use App\Events\SavercreationSms;
use App\Models\Moderator;
use Illuminate\Support\Str;
use Propaganistas\LaravelPhone\PhoneNumber;

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
        $business = Business::where("business_id", $user->business_id)->first();
        if ($business == null) {
            return response([
                "message" => "Business doesn't exist",
                "status" => "error"
            ], 400);
        }
        if ($business->balance < 200) {
            return response([
                "message" => "Business account balance is low",
                "status" => "error"
            ], 400);
        }
        $checkphone = Saver::where("phone", $request->phone_number)->where("business_id", $user->business_id)->first();
        if ($checkphone != null) {
            if ($checkphone->status == 1) {
                return response([
                    "message" => "Already registered",
                    "status" => "error",
                ], 200);
            }
            if ($checkphone->status == 0) {
                return response([
                    "message" => "Continue registration process.",
                    "status" => "incomplete",
                    "saver" => $checkphone
                ], 200);
            }
        }
        $otp_engine = new Otp();
        $otp =  Otp::setValidity(10)->setLength(4)->setOnlyDigits(true)->setUseSameToken(false)->generate("$request->phone_number/$user->business_id");
        event(new SavercreationSms($otp->token, $this->make($request->phone_number)));
        return response([
            "message" => "OTP Sent to phone number",
            "status" => "success",
            "opt" => $otp
        ], 200);
    } 

    public static function unmake($phone){
        return str_replace("0", "234", $phone);
        
    }
    public static function make($phone, $country = "NG")
    {
        $phoneNumber = PhoneNumber::make($phone, $country);
        return $phoneNumber;
    }

    public function SaverCreationStepTwo(Request $request) {
        $user = Auth::user();
        $request->validate([
            "phone_number" => "required|phone:NG",
            "token" => "required|string"
        ]);
        $id = Auth::id();
        $moderator = Moderator::where("id", $id)->first();

        $business = Business::where("business_id", $user->business_id)->first();
        if ($business == null) {
            return response([
                "message" => "Business doesn't exist",
                "status" => "error"
            ], 400);
        }
        if ($business->balance < 200) {
            return response([
                "message" => "Business account balance is low",
                "status" => "error"
            ], 400);
        }
        $business->withdraw(200);
        
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
            "firstname" => "required|string",
            "lastname" => "required|string",
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
        $hashed_random_password = rand(10000000, 99999999);

        $saver->update([
            "email" => $request->email,
            "password" => bcrypt($hashed_random_password),
            "password_string" => $hashed_random_password,
            "name" => $request->firstname.' '.$request->lastname,
            "status" => 1,
            "email_recievers" => json_encode([$request->email]),
            "logo" => "https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460__340.png"
        ]);
        $saver->save();

        return response([
            "status" => "success",
            "saver" => $saver,
            "message" => "Saver created successfully."
        ], 200);
        
    }
    public function listSavers(Request $request) {
        $request->validate([
            "page_number" => "required|integer",
            "saver_id" => "nullable|string"
        ]);
        
        if ($request->saver_id == null) {
            $savers = Saver::orderBy('id', 'DESC')->paginate($request->page_number);
            if ($savers->isNotEmpty()) {
                return response([
                    "status" => "success",
                    "savers" => $savers,
                    "message" => "Savers fetched successfully."
                ], 200);
            }
            if ($savers->isEmpty()) {
                return response([
                    "status" => "error",
                    "savers" => $savers,
                    "message" => "No savers available."
                ], 400);
            }
        }
        $savers = Saver::orderBy('id', 'DESC')->where("saver_id", $request->saver_id)->orWhere("phone", $request->saver_id)->paginate($request->page_number);
        if ($savers->isNotEmpty()) {
            return response([
                "status" => "success",
                "savers" => $savers,
                "message" => "Savers fetched successfully."
            ], 200);
        }
        if ($savers->isEmpty()) {
            return response([
                "status" => "error",
                "savers" => $savers,
                "message" => "No savers available."
            ], 400);
        }
    }
}
