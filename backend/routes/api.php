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
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\AdminController\AdminOverviewController;
use App\Http\Controllers\Api\AdminController\AdminUserController;
use App\Http\Controllers\Api\AdminController\AdminCampaignController;
use App\Http\Requests\Admin\RejectCampaignRequest;


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

        // Dashboard
        Route::get('dashboard/creator', [DashboardController::class, 'creator'])->name('dashboard.creator');
        Route::get('dashboard/funding-chart', [DashboardController::class, 'fundingChart'])->name('dashboard.funding-chart');
        Route::get('dashboard/backer', [DashboardController::class, 'backer'])->name('dashboard.backer');

        //Wallet
        Route::get('/wallet', [WalletController::class, 'index']);
        Route::post('/wallet/withdraw', [WalletController::class, 'withdraw']);
    });

    Route::prefix('admin')->middleware(['auth:sanctum','admin'])->group(function () {
        Route::get('dashboard', [AdminOverviewController::class, 'index'])
        ->name('admin.dashboard.overview');

        Route::get('dashboard/funding-chart', [AdminOverviewController::class, 'fundingChart'])
            ->name('admin.dashboard.funding-chart');

        Route::apiResource('user', AdminUserController::class)->only(['index', 'show', 'destroy']);

        Route::apiResource('campaign', AdminCampaignController::class)->except(['store','update']);

        Route::prefix('campaign/{campaign}')->group(function () {
            Route::patch('approve', [AdminCampaignController::class, 'approve'])->name('admin.campaign.approve');
            Route::patch('reject', [AdminCampaignController::class, 'reject'])->name('admin.campaign.reject');
            Route::get('campaign/review',[AdminCampaignController::class,'review']);
            Route::patch('campaign/{campaign}/force-fail',[AdminCampaignController::class,'forceFail'])->name('admin.campaign.force-fail');
        });
    });
});