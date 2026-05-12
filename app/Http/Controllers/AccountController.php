<?php

namespace App\Http\Controllers;

use App\Models\CreditTransaction;

class AccountController extends Controller
{
    public function credits()
    {
        $transactions = CreditTransaction::where('user_id', auth()->id())
            ->latest()
            ->paginate(30);

        return view('account.credits', compact('transactions'));
    }
}
