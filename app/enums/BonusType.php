<?php

namespace App\Enums;

enum BonusType: string
{
    case PairBonusNormal = 'pair_bonus_normal';
    case PairBonus2000 = 'pair_bonus_2000';
    case PairBonusStarter = 'pair_bonus_starter';
    case SponsorBinaryBonus = 'sponsor_binary_bonus';
    case RankReward = 'rank_reward';
    case LevelIncome = 'level_income';
    case DirectIncome = 'direct_income';
    case RewardAfterFullEmi = 'reward_after_full_emi';
    case EmiPayment = 'emi_payment';
    case PairBonus = 'pair_bonus';
    case Withdrawal = 'withdrawal';
    case FundRequestApproved = 'fund_request_approved';
    case Payout = 'payout';
    case FundRequest = 'fund_request';
    case commission = 'commission';
    case reward = 'reward';
    case other = 'other';

    /**
     * Every bonus type that counts as pair / matching income.
     *
     * PairBonus2000 is deliberately excluded — it is a separate scheme, not
     * part of pair income, and must not be added here.
     */
    public static function pairTypes(): array
    {
        return [
            self::PairBonusNormal->value,
            self::PairBonusStarter->value,
            self::PairBonus->value,
        ];
    }

    /**
     * Every pair type a user's daily binary income is summed from for the
     * sponsor binary bonus. Unlike pairTypes(), this includes PairBonus2000.
     */
    public static function sponsorBonusSourceTypes(): array
    {
        return [
            self::PairBonusNormal->value,
            self::PairBonus2000->value,
            self::PairBonusStarter->value,
            self::PairBonus->value,
        ];
    }
}
