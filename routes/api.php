<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\ModeratorController;
use App\Http\Controllers\SaverController;
use App\Http\Controllers\SavingsController;
use App\Http\Controllers\SavinglogController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['prefix' => 'v1'], function () {
    Route::group(['prefix' => 'admin'], function() {
        Route::post('/create', [AdminController::class, 'register']);
        Route::post('/login', [AdminController::class, 'login']);
        Route::get('/getadmin', [AdminController::class, 'getadmin'])->middleware(['auth:sanctum', 'type.admin']);
        Route::group(['prefix' => 'business'], function() {
            Route::post('/create', [BusinessController::class, 'register'])->middleware(['auth:sanctum', 'type.admin']);
            Route::post('/addfund', [BusinessController::class, 'addfund'])->middleware(['auth:sanctum', 'type.admin']);
            Route::post('/withdrawfund', [BusinessController::class, 'withdrawfund'])->middleware(['auth:sanctum', 'type.admin']);
        });      
    });


    Route::group(['prefix' => 'moderator'], function() {
        Route::post('/login', [ModeratorController::class, 'login']);
        Route::post('/logout', [ModeratorController::class, 'logout'])->middleware(['auth:sanctum', 'type.moderator']);
        Route::post('/addimage', [ModeratorController::class, 'addImage'])->middleware(['auth:sanctum', 'type.moderator']);
        Route::post('/updateprofile', [ModeratorController::class, 'updateprofile'])->middleware(['auth:sanctum', 'type.moderator']);
        Route::post('/setpin', [ModeratorController::class, 'setpin'])->middleware(['auth:sanctum', 'type.moderator']);
        Route::get('/getadmin', [ModeratorController::class, 'getadmin'])->middleware(['auth:sanctum', 'type.moderator']);
        Route::post('/slave/create', [ModeratorController::class, 'create_moderator'])->middleware(['auth:sanctum', 'type.moderator']);
        Route::group(['prefix' => "create/saver"], function() {
            Route::post('/stepone', [SaverController::class, 'SaverCreationStepOne'])->middleware(['auth:sanctum', 'type.moderator']);
            Route::post('/steptwo', [SaverController::class, 'SaverCreationStepTwo'])->middleware(['auth:sanctum', 'type.moderator']);
            Route::post('/stepthree', [SaverController::class, 'SaverCreationStepThree'])->middleware(['auth:sanctum', 'type.moderator']);
        });

        Route::group(['prefix' => "saver"], function() {
            Route::post('/list', [SaverController::class, 'listSavers'])->middleware(['auth:sanctum', 'type.moderator']); 
        });

        Route::group(['prefix' => 'savings'], function() {
            Route::post('/daily', [SavingsController::class, 'createsavingsdaily'])->middleware(['auth:sanctum', 'type.moderator']); 
            Route::post('/list', [SavingsController::class, 'listsavings'])->middleware(['auth:sanctum', 'type.moderator']); 
            Route::group(['prefix' => 'log'], function() {
                Route::post('/list', [SavinglogController::class, 'listsavingLog'])->middleware(['auth:sanctum', 'type.moderator']); 
            });
        });

    }); 

    
   
});
