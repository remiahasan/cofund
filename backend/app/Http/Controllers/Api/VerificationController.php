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

    public function verify(Request $request, $id, $hash)
    {
        $result = $this->verificationService->verifyUser((int) $id, $hash);
        $frontendUrl = config('app.frontend_url', 'http://localhost:5173');

        if ($result['status'] === 'invalid') {
            return redirect()->away("{$frontendUrl}/login?verified=0");
        }

        return redirect()->away("{$frontendUrl}/login?verified=1");
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

