<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function register(Request $request) {
        $request->validate([
            "name" => "required|string",
            "email" => "required|email",
            "password" => "required|string|confirmed"
        ]);
        $checkadmin = Admin::where("email", $request->email)->first();
        if ($checkadmin != null) {
            return response([
                "message" => "Email already exist.",
                "status" => "error"
            ], 400);
        }
        $admin =  Admin::create([
            "name" => $request->name,
            "admin_id" => "ADMIN".rand(100000,999999).rand(100000,999999).rand(100000,999999),
            "email" => $request->email,
            "password" => bcrypt($request->password)
        ]);
        return response([
            "admin" => $admin,
            "status" => "success",
            "message" => "Created successfully"
        ], 200);
    }

    public function getadmin() {
        $id = Auth::id();
        $admin = Admin::where("id", $id)->first();
        if ($admin == null) {
            return response([
                "status" => "error",
                "message" => "admin doesn't exist"
            ], 400);
        }
        return response([
            "status" => "success",
            "message" => "admin fetched successfully",
            "admin" => $admin
        ], 400);
    }

    public function login(Request $request) {
        $request->validate([
            "email" => "required|email",
            "password" => "required|string"
        ]);
        $customer = Admin::where('email', $request->email)->first();

        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return response([
                "message" => "The provided credentials are incorrect.",
                "status" => "error"
            ], 400);
        }
        return response([
            'customer' => $customer,
            "status" => "success",
            "message" => "Login Successful.", 
            'token' => $customer->createToken('webapp', ['role:admin'])->plainTextToken
        ]);
    }
}
