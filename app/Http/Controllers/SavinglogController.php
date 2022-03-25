<?php

namespace App\Http\Controllers;

use App\Models\Savinglog;
use Illuminate\Http\Request;

class SavinglogController extends Controller
{
    public function listsavingLog(Request $request){
        $request->validate([
            "savings_serial" => "required|string",
        ]);
        $savinglog = Savinglog::where("savings_serial", $request->savings_serial)->get();
        if ($savinglog->isEmpty()) {
            return response([
                "status" => "error",
                "message" => "No Savings Entries Listed",
                "savinglog" => $savinglog
            ], 400);    
        }
        if ($savinglog->isNotEmpty()) {
            return response([
                "status" => "success",
                "message" => "Savings Log Listed",
                "savinglog" => $savinglog
            ], 200);    
        }
    }
}
