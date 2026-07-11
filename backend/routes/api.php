<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\VerificationController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CampaignController;
use App\Http\Controllers\Api\CampaignTierController;
use App\Http\Controllers\Api\BackingController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\CampaignUpdateController;
use App\Http\Controllers\Api\NotificationController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    Route::get('/test', function () {
        return response()->json([
            'success' => true,
            'message' => 'Test berhasil.',
        ]);
    });

    // Authentication
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Verification
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
        ->middleware('signed')
        ->name('verification.verify');

    Route::middleware('auth:sanctum','verified')->group(function () {
        Route::get('/me', [AuthController::class, 'getAuthenticated']);
        Route::post('/logout', [AuthController::class, 'logout']);

        //Category
        Route::apiResource('category', CategoryController::class); 

        //Campaign
        Route::apiResource('campaign', CampaignController::class);

        // Campaign Updates
        Route::apiResource('campaign.update', CampaignUpdateController::class)->shallow();

        //Campaign Tiers
        Route::apiResource('campaign.tier', CampaignTierController::class)->shallow();

        //Backings
        Route::apiResource('backing', BackingController::class)->shallow();
        Route::patch('backing/{backing}/complete', [BackingController::class, 'complete']);

        //transaction
        Route::apiResource('transaction', TransactionController::class);
        Route::patch('transaction/{transaction}/mock-payment',[TransactionController::class, 'mockPayment']);

        // Notification
        Route::prefix('notification')->group(function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
            Route::get('/{notification}', [NotificationController::class, 'show']);
            Route::patch('/{notification}/read', [NotificationController::class, 'markAsRead']);
            Route::delete('/{notification}', [NotificationController::class, 'destroy']);
        });
    });
});