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
use App\Http\Controllers\Api\CampaignImageController;
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
        Route::get('category', [CategoryController::class, 'index']);
        Route::get('category/{category}', [CategoryController::class, 'show']);
        Route::post('category', [CategoryController::class, 'store']);
        Route::put('category/{category}', [CategoryController::class, 'update']);
        Route::delete('category/{category}', [CategoryController::class, 'destroy']);

        //Campaign
        Route::get('campaign', [CampaignController::class, 'index']);
        Route::get('campaign/{campaign}', [CampaignController::class, 'show']);
        Route::post('campaign', [CampaignController::class, 'store']);
        Route::put('campaign/{campaign}', [CampaignController::class, 'update']);
        Route::delete('campaign/{campaign}', [CampaignController::class, 'destroy']);
        Route::patch('campaign/{campaign}/to-review', [CampaignController::class, 'toReview']);

        Route::get('campaign/{campaign}/images', [CampaignImageController::class, 'index']);
        Route::post('campaign/{campaign}/images', [CampaignImageController::class, 'store']);
        Route::put('/campaign/{campaign}/images/{image}', [CampaignImageController::class, 'update']);
        Route::delete('/campaign/{campaign}/images/{image}', [CampaignImageController::class, 'destroy']);
        Route::patch('campaign-image/{image}/set-primary', [CampaignImageController::class, 'setPrimary']);

        // Campaign Updates
        Route::get('campaign/{campaign}/update', [CampaignUpdateController::class, 'index']);
        Route::get('campaign/{campaign}/update/{update}', [CampaignUpdateController::class, 'show']);
        Route::post('campaign/{campaign}/update', [CampaignUpdateController::class, 'store']);
        Route::put('campaign/{campaign}/update/{update}', [CampaignUpdateController::class, 'update']);
        Route::delete('campaign/{campaign}/update/{update}', [CampaignUpdateController::class, 'destroy']);

        //Campaign Tiers
        Route::get('campaign-tier', [CampaignTierController::class, 'index']);
        Route::get('campaign-tier/{campaign_tier}', [CampaignTierController::class, 'show']);
        Route::post('campaign-tier', [CampaignTierController::class, 'store']);
        Route::put('campaign-tier/{campaign_tier}', [CampaignTierController::class, 'update']);
        Route::delete('campaign-tier/{campaign_tier}', [CampaignTierController::class, 'destroy']);

        //Backings
        Route::get('campaign/{campaign}/backing', [BackingController::class, 'index']);
        Route::get('backing/{backing}', [BackingController::class, 'show']);
        Route::post('backing', [BackingController::class, 'store']);
        Route::put('backing/{backing}', [BackingController::class, 'update']);
        Route::delete('backing/{backing}', [BackingController::class, 'destroy']);
        Route::patch('backing/{backing}/complete', [BackingController::class, 'complete']);

        //transaction
        Route::get('transaction', [TransactionController::class, 'index']);
        Route::get('transaction/{transaction}', [TransactionController::class, 'show']);
        Route::post('transaction', [TransactionController::class, 'store']);
        Route::put('transaction/{transaction}', [TransactionController::class, 'update']);
        Route::delete('transaction/{transaction}', [TransactionController::class, 'destroy']);
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
        Route::post('/wallet/topup', [WalletController::class, 'topup']);
        Route::post('/wallet/withdraw', [WalletController::class, 'withdraw']);
    });

    Route::prefix('admin')->middleware(['auth:sanctum','admin'])->group(function () {
        Route::get('dashboard', [AdminOverviewController::class, 'index'])
        ->name('admin.dashboard.overview');

        Route::get('dashboard/funding-chart', [AdminOverviewController::class, 'fundingChart'])
            ->name('admin.dashboard.funding-chart');

        Route::get('user', [AdminUserController::class, 'index']);
        Route::get('user/{user}', [AdminUserController::class, 'show']);
        Route::delete('user/{user}', [AdminUserController::class, 'destroy']);

        Route::get('campaign', [AdminCampaignController::class, 'index']);
        Route::get('campaign/review', [AdminCampaignController::class, 'review']);
        Route::get('campaign/{campaign}', [AdminCampaignController::class, 'show']);

        Route::prefix('campaign/{campaign}')->group(function () {
            Route::patch('approve', [AdminCampaignController::class, 'approve'])->name('admin.campaign.approve');
            Route::patch('reject', [AdminCampaignController::class, 'reject'])->name('admin.campaign.reject');
            Route::patch('force-fail',[AdminCampaignController::class,'forceFail'])->name('admin.campaign.force-fail');
        });
    });
});