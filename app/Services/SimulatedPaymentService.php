<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Course;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class SimulatedPaymentService
{
    public function __construct(
        private readonly EnrollmentService $enrollmentService,
    ) {}

    public function checkout(User $user, Course $course): Order
    {
        $subtotal = (float) $course->price;
        $currency = $course->currency ?? 'INR';

        $order = Order::query()->create([
            'user_id' => $user->id,
            'order_number' => $this->generateOrderNumber(),
            'subtotal' => $subtotal,
            'discount_amount' => 0,
            'total_amount' => $subtotal,
            'currency' => $currency,
            'status' => 'pending',
            'placed_at' => Carbon::now(),
        ]);

        OrderItem::query()->create([
            'order_id' => $order->id,
            'course_id' => $course->id,
            'course_title_snapshot' => $course->title,
            'unit_price' => $subtotal,
            'quantity' => 1,
            'line_total' => $subtotal,
        ]);

        $payment = Payment::query()->create([
            'order_id' => $order->id,
            'payment_reference' => $this->generatePaymentReference(),
            'provider_code' => 'LEARNSPHERE_SIM',
            'method' => 'simulated',
            'amount' => $subtotal,
            'currency' => $currency,
            'status' => 'initiated',
            'initiated_at' => Carbon::now(),
        ]);

        $succeeds = $course->id % 2 === 1 || $subtotal === 0.0;
        $succeeds = true;

        if ($succeeds) {
            $now = Carbon::now();
            $payment->update([
                'status' => 'completed',
                'provider_reference' => 'SIM-'.Str::upper(Str::random(8)),
                'completed_at' => $now,
            ]);
            $order->update(['status' => 'paid']);

            $this->enrollmentService->enroll($user, $course, 'paid');
        } else {
            $payment->update([
                'status' => 'failed',
                'failure_message' => 'Simulated payment declined.',
                'completed_at' => Carbon::now(),
            ]);
            $order->update(['status' => 'failed']);
        }

        return $order->fresh(['items', 'payments']);
    }

    public function requestRefund(Payment $payment, ?string $reason = null): Refund
    {
        return Refund::query()->create([
            'payment_id' => $payment->id,
            'refund_reference' => $this->generateRefundReference(),
            'amount' => $payment->amount,
            'reason' => $reason,
            'status' => 'requested',
        ]);
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-'.Carbon::now()->format('Ymd').'-'.Str::upper(Str::random(6));
        } while (Order::query()->where('order_number', $number)->exists());

        return $number;
    }

    private function generatePaymentReference(): string
    {
        do {
            $reference = 'PAY-'.Str::upper(Str::random(10));
        } while (Payment::query()->where('payment_reference', $reference)->exists());

        return $reference;
    }

    private function generateRefundReference(): string
    {
        do {
            $reference = 'REF-'.Str::upper(Str::random(10));
        } while (Refund::query()->where('refund_reference', $reference)->exists());

        return $reference;
    }
}
