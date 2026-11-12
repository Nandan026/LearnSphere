<?php

declare(strict_types=1);

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Order;
use App\Services\SimulatedPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        private readonly SimulatedPaymentService $paymentService,
    ) {}

    public function checkout(Course $course): View
    {
        abort_unless($course->status === 'published' && (float) $course->price > 0, 404);

        return view('learner.orders.checkout', compact('course'));
    }

    public function place(Course $course): RedirectResponse
    {
        $order = $this->paymentService->checkout(Auth::user(), $course);

        if ($order->status === 'paid') {
            return redirect()->route('learner.player.show', $course)->with('success', 'Payment successful. You are enrolled.');
        }

        return redirect()->route('learner.orders.index')->with('error', 'Payment failed.');
    }

    public function index(): View
    {
        $orders = Order::query()
            ->where('user_id', Auth::id())
            ->with('items')
            ->latest('placed_at')
            ->paginate(15);

        return view('learner.orders.index', compact('orders'));
    }
}
