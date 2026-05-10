<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    /**
     * Read-only balance and history (e.g. refunds / legacy credits).
     * Deals are paid by card only — wallet top-ups have been removed.
     */
    public function index()
    {
        $user = Auth::user();
        $transactions = $user->walletTransactions()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('wallet.index', [
            'walletBalance' => $user->wallet_balance,
            'transactions' => $transactions,
        ]);
    }
}
