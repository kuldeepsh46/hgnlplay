<?php

namespace App\Enums;

enum BonusType: string
{
    case PairBonusNormal = 'pair_bonus_normal';
    case PairBonus2000 = 'pair_bonus_2000';
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
}