<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Moderator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ModeratorController extends Controller
{
    public function login(Request $request) {
        $request->validate([
            "email" => "required|email",
            "password" => "required|string"
        ]);
        $moderator = Moderator::where('email', $request->email)->first();

        if (!$moderator || !Hash::check($request->password, $moderator->password)) {
            return response([
                "message" => "The provided credentials are incorrect.",
                "status" => "error"
            ], 400);
        }
        $moderator->business;
        return response([
            'moderator' => $moderator,
            "status" => "success",
            "message" => "Login Successful.", 
            'token' => $moderator->createToken('webapp', ['role:moderator'])->plainTextToken
        ]);
    }

    public function logout() {
        $id = Auth::id();
        $customer = Moderator::where("id", $id)->first();
        if ($customer == null) {
            return response([
                "message" => "Moderator does'nt exist.",
                "status" => "error"
            ], 400);
        }
        Auth::user()->tokens()->delete();
        return response([
            "status" => "success",
            "message" => "Moderator logout successful.",
        ], 200);
    }

    public function create_moderator(Request $request) {
        $user = Auth::user(); 
        $request->validate([
            // "business_id" => "required|string",
            "password" => "required|string",
            "name"  => "required|string",
            "email" => "required|email"
        ]);
        
        $checkemail = Moderator::where("email", $request->email)->first();
        if($checkemail != null) {
            return response([
                "message" => "Email exist already",
                "status" => "error"
            ],400);
        }
        if ($user->type == 'slave') {
            return response([
                "message" => "You can't perform this",
                "status" => "error"
            ],400);
        }
        $newmoderator = Moderator::create([
            "business_id" => Auth::user()->business_id,
            "name" => $request->name,
            "moderator_id" => "MOD".rand(1000000000,9999999999),
            "email" => $request->email,
            "password" => bcrypt($request->password)
        ]);
        if ($newmoderator != null) {
            $newmoderator->business;
        }
        return response([
            "message" => "Moderators Created Successfully.",
            "status" => "success",
            "moderator" => $newmoderator
        ], 200);
    }
    public function setpin(Request $request) {
        $request->validate([
            "transaction_pin" => "required|string"
        ]);
        $id = Auth::id();
        $moderator = Moderator::where("id", $id)->first();
        if($moderator == null) {
            return response([
                "message" => "Moderator doesn't exist",
                "status" => "error"
            ],400);
        }
        $moderator->update(["transaction_pin" => $request->transaction_pin]);
        return response([
            "message" => "Moderator Transaction pin updated",
            "status" => "success"
        ],200);
    }

    public function updateprofile(Request $request) {
        $request->validate([
            "name"  => "nullable|string",
            "email" => "nullable|email",
            "phone" => "nullable|string"
        ]);
        $id = Auth::id();
        $checkemail = Moderator::where("email", $request->email)->where("id", '!=',$id)->first();
        if ($checkemail != null) {
            return response([
                "message" => "thes email already exist",
                "status" => "error"
            ],400);
        }
        $checkphone = Moderator::where("phone", $request->phone)->where("id", '!=',$id)->first();
        if ($checkphone != null) {
            return response([
                "message" => "these phone number already exist",
                "status" => "error"
            ],400);
        }
        $moderator = Moderator::where("id", $id)->first();
        if($moderator == null) {
            return response([
                "message" => "Moderator doesn't exist",
                "status" => "error"
            ],400);
        }
        $moderator->update($request->all());
        $moderator->save();
        $moderator->business;
        return response([
            "status" => "success",
            "message" => "moderator updated successfully",
            "moderator" => $moderator
        ], 200);
    }
    public function addImage(Request $request) {
        $request->validate([
            "image" => "required|string"
        ]);
        $id = Auth::id();
        $moderator = Moderator::where("id", $id)->first();
        if ($moderator == null) {
            return response([
                "status" => "error",
                "message" => "moderator doesn't exist"
            ], 400);
        }
        $moderator->update([
            "logo" => $request->image
        ]);
        $moderator->save();
        return response([
            "status" => "success",
            "message" => "moderator updated successfully",
            "moderator" => $moderator
        ], 200);
    }

    public function getadmin() {
        $id = Auth::id();
        $moderator = Moderator::where("id", $id)->first();
        if ($moderator == null) {
            return response([
                "status" => "error",
                "message" => "moderator doesn't exist"
            ], 400);
        }

        // $moderator->assignRole([4]);
        return response([
            "status" => "success",
            "message" => "moderator fetched successfully",
            "moderator" => $moderator
        ], 400);
    }
}
