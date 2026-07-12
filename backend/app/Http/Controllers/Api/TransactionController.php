<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\StoreTransactionRequest;
use App\Http\Requests\Transaction\UpdateTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    public function __construct(
        private readonly TransactionService $transactionService
    ) {}
    
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $transactions = $this->transactionService->getTransaction();
        
        return $this->success(
            'Daftar Transaksi Berhasil Diambil',
            TransactionResource::collection($transactions),
            [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request): JsonResponse
    {
        $transaction = $this->transactionService->storeTransaction($request->validated(), auth()->user());
        
        return $this->success(
            'Transaksi berhasil dibuat',
            new TransactionResource($transaction)
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction): JsonResponse
    {
        $transaction = $this->transactionService->showTransaction($transaction);
        
        return $this->success(
            'Transaksi berhasil diambil',
            new TransactionResource($transaction)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction): JsonResponse
    {
        $transaction = $this->transactionService->updateTransaction($transaction, $request->validated());
        
        return $this->success(
            'Transaksi berhasil diupdate',
            new TransactionResource($transaction)
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction): JsonResponse
    {
        $this->transactionService->deleteTransaction($transaction);
        
        return $this->success('Transaksi berhasil dihapus');
    }

    public function mockPayment(Transaction $transaction): JsonResponse
    {
        $transaction = $this->transactionService->mockPayment($transaction);

        return $this->success(
            'Pembayaran berhasil.',
            new TransactionResource($transaction)
        );
    }
}

