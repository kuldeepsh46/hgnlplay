<?php
namespace App\Services;

namespace App\Services;

use App\Models\User;
use App\Models\UserMatrixProgress;
use Illuminate\Support\Facades\DB;

class MatrixService
{
    /**
     * @param User $buyer
     */
    public function processCommission(User $buyer)
    {
        // 1. Ensure the buyer has a sponsor
        if (!$buyer->sponsor_id) {
            return;
        }

        // 2. Safely find the 3-level upline
        $sponsor1 = User::find($buyer->sponsor_id);
        $sponsor2 = $sponsor1 ? User::find($sponsor1->sponsor_id) : null;
        $sponsor3 = $sponsor2 ? User::find($sponsor2->sponsor_id) : null;

        $uplineChain = [1 => $sponsor1, 2 => $sponsor2, 3 => $sponsor3];
        $rates = [1 => 100, 2 => 200, 3 => 700];

        foreach ($uplineChain as $tier => $user) {
            if (!$user) {
                continue;
            }

            // Get or Create progress record
            $progress = UserMatrixProgress::firstOrCreate(['user_id' => $user->id]);
            $tierField = "tier_{$tier}_count";

            // Limits
            $max = $tier == 1 ? 3 : ($tier == 2 ? 9 : 27);

            if ($progress->$tierField < $max) {
                DB::transaction(function () use ($user, $progress, $tierField, $rates, $tier) {
                    // 1. Update the balance in the 'wallets' table
                    DB::table('wallets')->where('user_id', $user->id)->increment('balance', $rates[$tier]);

                    // 2. Increment the progress count
                    $progress->increment($tierField);
                });

                $this->checkPromotion($user, $progress);
            }
        }
    }

    private function checkPromotion(User $user, UserMatrixProgress $progress)
    {
        if ($progress->tier_1_count == 3 && $progress->tier_2_count == 9 && $progress->tier_3_count == 27) {
            $progress->update([
                'rank_level' => $progress->rank_level + 1,
                'tier_1_count' => 0,
                'tier_2_count' => 0,
                'tier_3_count' => 0,
            ]);
        }
    }
}
