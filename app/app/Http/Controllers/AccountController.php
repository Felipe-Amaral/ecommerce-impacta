<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class AccountController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();

        $orders = $user->orders()
            ->withCount(['items', 'messages'])
            ->with('payments')
            ->latest()
            ->limit(12)
            ->get();

        $addresses = $user->addresses()
            ->latest()
            ->get();

        return view('account.dashboard', compact('user', 'orders', 'addresses'));
    }
}
