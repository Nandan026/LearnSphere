<?php

declare(strict_types=1);

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->with('roles')
            ->when($request->filled('q'), fn ($q) => $q->where('email', 'like', '%'.$request->string('q').'%'))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('support.users.index', compact('users'));
    }

    public function show(User $user): View
    {
        $user->load(['roles', 'enrollments.course']);

        return view('support.users.show', compact('user'));
    }
}
