<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\Business;
use App\Models\Moderator;

class BusinessController extends Controller
{
    
    public function register(Request $request) {
        $request->validate([
            "business_name" => "required|string",
            "logo" => "required|string",
            "name" => "required|string",
            "email" => "required|email",
            "business_description" => "required|string",
            "password" => "required|string"
        ]);
        $id = Auth::id();
        $admin = Admin::where("id", $id)->first();
        if ($admin == null) {
            return response([
                "message" => "Admin does't exist",
                "status" => "error"
            ],400);
        }
        if($admin->type != "master") {
            return response([
                "message" => "You don't have permission to do this.",
                "status" => "error"
            ],400);
        }   
        $checkemail = Moderator::where("email", $request->email)->first();
        if($checkemail != null) {
            return response([
                "message" => "Email Exist already",
                "status" => "error"
            ],400);
        }
        $business = Business::create([
            "business_name" => $request->business_name,
            "logo" => $request->logo,
            "business_description" => $request->business_description,
            "admin_id" => $admin->admin_id,
            "status" => 0,
            "business_id" => "BUS".rand(1000000000,9999999999)
        ]);
        $moderator = Moderator::create([
            "business_id" => $business->business_id,
            "type" => "master",
            "name" => $request->name,
            "email" => $request->email,
            "password" => bcrypt($request->password)
        ]);
        $business->moderator = $moderator;
        return response([
            "massage" => "Business Created Successfully",
            "status" => "success",
            "business" => $business
        ], 200);
    }

    public function addfund(Request $request) {
        $request->validate([
            "amount" => "required|integer",
            "business_id" => "required|string"
        ]);
        if ($request->amount < 100) {
            return response([
                "message" => "Amount should be more than 100",
                "status" => "error"
            ], 400);
        }
        $business = Business::where("business_id", $request->business_id)->first();
        if ($business == null) {
            return response([
                "message" => "Business doesn't exist",
                "status" => "error"
            ], 400);
        }
        $business->deposit($request->amount);
        return response([
            "message" => "business funded successfully",
            "status" => "success",
            "business" => $business
        ], 200);
    }


    public function withdrawfund(Request $request) {
        $request->validate([
            "amount" => "required|integer",
            "business_id" => "required|string"
        ]);
        if ($request->amount < 100) {
            return response([
                "message" => "Amount should be more than 100",
                "status" => "error"
            ], 400);
        }
        $business = Business::where("business_id", $request->business_id)->first();
        if ($business == null) {
            return response([
                "message" => "Business doesn't exist",
                "status" => "error"
            ], 400);
        }
        if ($business->balance < $request->amount) {
            return response([
                "message" => "Amount is greater than balance",
                "status" => "error"
            ], 400);
        }
        $business->withdraw($request->amount);
        return response([
            "message" => "business withdrawn successfully",
            "status" => "success",
            "business" => $business
        ], 200);
    }

}
