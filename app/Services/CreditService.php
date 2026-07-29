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
        return DB::transaction(function () use ($user, $amount, $description) {
            $locked = User::whereKey($user->id)->lockForUpdate()->first();

            if ($locked->credits < $amount) {
                return false;
            }

            $locked->decrement('credits', $amount);
            $user->credits = $locked->credits;

            CreditTransaction::create([
                'user_id'     => $locked->id,
                'amount'      => -$amount,
                'type'        => 'purchase',
                'description' => $description,
            ]);

            return true;
        });
    }
}
