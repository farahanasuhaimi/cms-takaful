<?php

namespace App\Services;

use App\Models\CreditTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreditService
{
    public static function award(User $user, int $amount, string $type, string $description): void
    {
        DB::transaction(function () use ($user, $amount, $type, $description) {
            $user->increment('credits', $amount);
            CreditTransaction::create([
                'user_id'     => $user->id,
                'amount'      => $amount,
                'type'        => $type,
                'description' => $description,
            ]);
        });
    }

    public static function spend(User $user, int $amount, string $description): bool
    {
        if ($user->credits < $amount) {
            return false;
        }

        DB::transaction(function () use ($user, $amount, $description) {
            $user->decrement('credits', $amount);
            CreditTransaction::create([
                'user_id'     => $user->id,
                'amount'      => -$amount,
                'type'        => 'purchase',
                'description' => $description,
            ]);
        });

        return true;
    }
}
