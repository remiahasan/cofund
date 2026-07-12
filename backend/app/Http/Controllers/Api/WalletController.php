<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\WalletService;
use App\Http\Resources\WalletResource;
use App\Http\Resources\TransactionResource;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\WithdrawRequest;
use App\Http\Requests\TopUpRequest;

class WalletController extends Controller
{
    public function __construct(
        private readonly WalletService $walletService
    ) {}

    public function index(): JsonResponse
    {
        $balance = $this->walletService->balance(auth()->user());

        return $this->success(
            'Saldo berhasil diambil.',
            new WalletResource($balance)
        );
    }

    public function topup(TopUpRequest $request): JsonResponse
    {
        $transaction = $this->walletService->topUp(
            auth()->user(),
            $request->amount
        );

        return $this->success(
            'Top up berhasil.',
            new TransactionResource($transaction)
        );
    }

    public function withdraw(WithdrawRequest $request): JsonResponse 
    {
        $transaction = $this->walletService->withdraw(
            auth()->user(),
            $request->amount
        );

        return $this->success(
            'Withdraw berhasil.',
            new TransactionResource($transaction)
        );
    }
}

