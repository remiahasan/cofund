<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\VerificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VerificationController extends Controller
{
    public function __construct(
        private readonly VerificationService $verificationService
    ) {}

    public function verify(Request $request, $id, $hash): JsonResponse
    {
        $result = $this->verificationService->verifyUser((int) $id, $hash);

        if ($result['status'] === 'invalid') {
            return $this->error($result['message'], null, 401);
        }

        return $this->success($result['message']);
    }

    public function resend(Request $request): JsonResponse
    {
        $sent = $this->verificationService->resendVerification($request->user());

        if (!$sent) {
            return $this->error('Email sudah diverifikasi.', null, 400);
        }

        return $this->success('Email verifikasi berhasil dikirim ulang.');
    }
}

