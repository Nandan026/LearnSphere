<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Certificate;
use App\Models\Enrollment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class CertificateService
{
    public function issueIfEligible(Enrollment $enrollment): ?Certificate
    {
        if ($enrollment->completed_at === null) {
            return null;
        }

        $existing = Certificate::query()
            ->where('enrollment_id', $enrollment->id)
            ->first();

        if ($existing !== null) {
            return $existing;
        }

        return Certificate::query()->create([
            'enrollment_id' => $enrollment->id,
            'user_id' => $enrollment->user_id,
            'course_id' => $enrollment->course_id,
            'certificate_code' => $this->generateUniqueCode(),
            'issued_at' => Carbon::now(),
            'status' => 'issued',
        ]);
    }

    private function generateUniqueCode(): string
    {
        do {
            $code = 'LS-'.Str::upper(Str::random(6));
        } while (Certificate::query()->where('certificate_code', $code)->exists());

        return $code;
    }
}
