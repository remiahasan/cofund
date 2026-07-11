<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\WalletService;
use App\Http\Resources\WalletResource;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\WithdrawRequest;
use App\Models\Transaction;

class WalletController extends Controller
{
    public function __construct(
        private WalletService $walletService
    ) {}

    public function balance(): JsonResponse
    {
        $balance = $this->walletService->balance(auth()->user());

        return response()->json([
            'success' => true,
            'data' => new WalletResource($balance),
        ]);
    }

    public function withdraw(WithdrawRequest $request): JsonResponse 
    {
        $transaction = $this->walletService->withdraw(
            auth()->user(),
            $request->amount
        );

        return response()->json([
            'success' => true,
            'message' => 'Withdraw berhasil.',
            'data' => $transaction,
        ]);
    }
}
