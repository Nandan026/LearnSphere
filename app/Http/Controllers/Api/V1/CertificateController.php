<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\JsonResponse;

class CertificateController extends Controller
{
    public function verify(string $code): JsonResponse
    {
        $certificate = Certificate::query()
            ->with(['user:id,name', 'course:id,title'])
            ->where('certificate_code', strtoupper($code))
            ->where('status', 'issued')
            ->first();

        if ($certificate === null) {
            return response()->json(['valid' => false], 404);
        }

        return response()->json([
            'valid' => true,
            'code' => $certificate->certificate_code,
            'issued_at' => $certificate->issued_at,
            'learner' => $certificate->user?->name,
            'course' => $certificate->course?->title,
        ]);
    }
}
