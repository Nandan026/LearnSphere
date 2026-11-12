<?php

declare(strict_types=1);

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function index(): View
    {
        $certificates = Certificate::query()
            ->where('user_id', Auth::id())
            ->with('course')
            ->latest('issued_at')
            ->get();

        return view('learner.certificates.index', compact('certificates'));
    }

    public function show(Certificate $certificate): View
    {
        abort_unless($certificate->user_id === Auth::id(), 403);
        $certificate->load(['course', 'user']);

        return view('learner.certificates.show', compact('certificate'));
    }
}
