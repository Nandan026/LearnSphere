<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CertificateVerificationController extends Controller
{
    public function show(): View
    {
        return view('certificates.verify');
    }

    public function verify(Request $request): View
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:32'],
        ]);

        $code = strtoupper(trim($data['code']));

        $certificate = Certificate::query()
            ->with(['user', 'course'])
            ->where('certificate_code', $code)
            ->where('status', 'issued')
            ->first();

        return view('certificates.verify', [
            'code' => $code,
            'certificate' => $certificate,
            'verified' => $certificate !== null,
        ]);
    }
}
