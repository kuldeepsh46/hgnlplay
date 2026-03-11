<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TreeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // You can allow ?user_id=XYZ to see someone else's tree (optional)
        $rootId = $request->get('user_id', $user->id);

        $tree = $this->buildTree($rootId);

        $MAX_DEPTH = 8;
        $user = auth()->user();

        $levels = [];
        $levels[0] = [
            [
                'id' => $user->id,
                'username' => $user->username,
                'name' => $user->name,
                'color' => $this->getColor($user->investment_count ?? 0),
                // 'nm' => 'ks'
            ],
        ];

        for ($level = 1; $level < $MAX_DEPTH; $level++) {
            $levels[$level] = [];

            foreach ($levels[$level - 1] as $parent) {
                if (isset($parent['blank'])) {
                    // Blank parent → two blank children
                    $levels[$level][] = ['blank' => true];
                    $levels[$level][] = ['blank' => true];
                    continue;
                }

                $children = User::where('placement_id', $parent['id'])->get();

                $left = $children->where('position', 'left')->first();
                $right = $children->where('position', 'right')->first();

                $levels[$level][] = $left
                    ? [
                        'id' => $left->id,
                        'username' => $left->username,
                        'name' => $left->name,
                        'color' => $this->getColor($left->investment_count ?? 0),
                    ]
                    : ['blank' => true];

                $levels[$level][] = $right
                    ? [
                        'id' => $right->id,
                        'username' => $right->username,
                        'name' => $right->name,
                        'color' => $this->getColor($right->investment_count ?? 0),
                    ]
                    : ['blank' => true];
            }
        }

        return view('team_tree', compact('levels', 'user', 'tree'));

        // return view('team_tree', compact('user', 'tree'));
    }
    // private function getBranchTotalRecursive($userId)
    // {
    //     $user = User::with('orders.package')->find($userId);
    //     if (!$user) {
    //         return 0;
    //     }

    //     // Summing the 'amount' column from the packages related to this user's orders
    //     $personalInvestment = $user->orders->sum(function ($order) {
    //         return $order->package->amount ?? 0;
    //     });

    //     $left = User::where('placement_id', $user->id)->where('position', 'left')->first();
    //     $right = User::where('placement_id', $user->id)->where('position', 'right')->first();

    //     return $personalInvestment + ($left ? $this->getBranchTotalRecursive($left->id) : 0) + ($right ? $this->getBranchTotalRecursive($right->id) : 0);
    // }
    private function getBranchTotalRecursive($userId)
    {
        $user = User::with('orders.package')->find($userId);
        if (!$user) {
            return 0;
        }

        $personalInvestment = 0;
        if ($user->orders) {
            $personalInvestment = $user->orders->sum(function ($order) {
                return optional($order->package)->amount ?? 0;
            });
        }

        $left = User::where('placement_id', $user->id)->where('position', 'left')->first();
        $right = User::where('placement_id', $user->id)->where('position', 'right')->first();

        return $personalInvestment + ($left ? $this->getBranchTotalRecursive($left->id) : 0) + ($right ? $this->getBranchTotalRecursive($right->id) : 0);
    }
    /**
     * Build the entire binary tree recursively
     */
    // private function buildTree($userId)
    // {
    //     $user = User::find($userId);
    //     if (!$user) {
    //         return null;
    //     }

    //     $left = User::where('placement_id', $user->id)->where('position', 'left')->first();

    //     $right = User::where('placement_id', $user->id)->where('position', 'right')->first();
    //     $leftBusiness = $left ? $this->getTotalBranchBusiness($left->id) : 0;
    //     $rightBusiness = $right ? $this->getTotalBranchBusiness($right->id) : 0;
    //     return [
    //         'id' => $user->id,
    //         'username' => $user->username,
    //         'investment_count' => $user->investment_count ?? 0,
    //         'color' => $this->getColor($user->investment_count ?? 0),
    //         'left' => $left ? $this->buildTree($left->id) : null,
    //         'right' => $right ? $this->buildTree($right->id) : null,
    //         // 'nm' => 'ks'
    //         'left_business' => $leftBusiness,
    //         'right_business' => $rightBusiness,
    //     ];
    // }
    // private function buildTree($userId)
    // {
    //     $user = User::with('orders.package')->find($userId);
    //     if (!$user) {
    //         return null;
    //     }

    //     // Get personal investment using Eloquent
    //     $currentUserInvestment = $user->orders->sum(function ($order) {
    //         return $order->package->amount ?? 0;
    //     });

    //     $left = User::where('placement_id', $user->id)->where('position', 'left')->first();
    //     $right = User::where('placement_id', $user->id)->where('position', 'right')->first();

    //     // Calculate business beneath them
    //     $leftBusiness = $left ? $this->getBranchTotalRecursive($left->id) : 0;
    //     $rightBusiness = $right ? $this->getBranchTotalRecursive($right->id) : 0;

    //     // Side Counts (Total people)
    //     $leftCount = $left ? $this->getBranchCountRecursive($left->id) : 0;
    //     $rightCount = $right ? $this->getBranchCountRecursive($right->id) : 0;

    //     return [
    //         'id' => $user->id,
    //         'username' => $user->username,
    //         'personal_investment' => $currentUserInvestment,
    //         'investment_count' => $currentUserInvestment > 0 ? 1 : 0,
    //         'left_business' => $leftBusiness,
    //         'right_business' => $rightBusiness,
    //         'left_count' => $leftCount,
    //         'right_count' => $rightCount,
    //         'left' => $left ? $this->buildTree($left->id) : null,
    //         'right' => $right ? $this->buildTree($right->id) : null,
    //     ];
    // }
    private function buildTree($userIdOrMemberId)
{
    // 1. Updated Search Logic: Find user by member_id OR internal id
    $user = User::with('orders.package')
        ->where('member_id', $userIdOrMemberId)
        ->orWhere('id', $userIdOrMemberId)
        ->first();

    if (!$user) return [
        'id' => null, 
        'member_id' => null,
        'total_business' => 0, 
        'total_contributors' => 0
    ];

    // 2. Calculate this specific user's investment
    $personalInvestment = $user->orders->sum(function($order) {
        // Updated to use order amount as we discussed earlier for accuracy
        return $order->amount ?? 0;
    });

    // 3. Get Children Data (Recursive calls stay the same)
    $leftUser = User::where('placement_id', $user->id)->where('position', 'left')->first();
    $rightUser = User::where('placement_id', $user->id)->where('position', 'right')->first();

    $leftNode = $leftUser ? $this->buildTree($leftUser->id) : null;
    $rightNode = $rightUser ? $this->buildTree($rightUser->id) : null;

    // 4. Calculate Totals for THIS node based on children
    $leftBranchTotal = $leftNode ? ($leftNode['personal_investment'] + $leftNode['total_business']) : 0;
    $rightBranchTotal = $rightNode ? ($rightNode['personal_investment'] + $rightNode['total_business']) : 0;

    $leftBranchContributors = $leftNode ? ($leftNode['is_contributor'] + $leftNode['total_contributors']) : 0;
    $rightBranchContributors = $rightNode ? ($rightNode['is_contributor'] + $rightNode['total_contributors']) : 0;

    return [
        'id' => $user->id,
        'member_id' => $user->member_id, // Added member_id to the array
        'username' => $user->username,
        'personal_investment' => $personalInvestment,
        'is_active' => ($personalInvestment > 0),
        'is_contributor' => ($personalInvestment > 0 ? 1 : 0),
        
        'total_business_left' => $leftBranchTotal,
        'total_contributors_left' => $leftBranchContributors,
        'total_business_right' => $rightBranchTotal,
        'total_contributors_right' => $rightBranchContributors,

        'left' => $leftNode,
        'right' => $rightNode,
        
        'total_business' => $leftBranchTotal + $rightBranchTotal, 
        'total_contributors' => $leftBranchContributors + $rightBranchContributors
    ];
}

    // Helper to count people on each side
    private function getBranchCountRecursive($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return 0;
        }

        $left = User::where('placement_id', $user->id)->where('position', 'left')->first();
        $right = User::where('placement_id', $user->id)->where('position', 'right')->first();

        return 1 + ($left ? $this->getBranchCountRecursive($left->id) : 0) + ($right ? $this->getBranchCountRecursive($right->id) : 0);
    }

    private function buildTreeOLD($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return null;
        }

        // Get all users directly under this user
        $children = User::where('placement_id', $user->id)->get();

        $left = $children->where('position', 'left')->first();
        $right = $children->where('position', 'right')->first();

        return [
            'id' => $user->id,
            'username' => $user->username,
            'name' => $user->name,
            'investment_count' => $user->investment_count ?? 0,
            'color' => $this->getColor($user->investment_count ?? 0),
            'left' => $left ? $this->buildTree($left->id) : null, // 👈 Recursive left
            'right' => $right ? $this->buildTree($right->id) : null, // 👈 Recursive right
        ];
    }

    public function list()
    {
        $user = Auth::user();

        // Get all downline users recursively (any depth)
        // $teamMembers = $user->allDescendants();
        // $teamMembers = $user->allDescendants()->sortBy('created_at');
        $teamMembers = $user->allDescendants()->reject(fn($member) => $member->id == 27)->sortBy('created_at');

        return view('team.list', compact('user', 'teamMembers'));
    }

    private function buildLevels($userId, $level = 0, &$levels = [])
    {
        $user = User::find($userId);
        if (!$user) {
            return;
        }

        $levels[$level][] = [
            'id' => $user->id,
            'username' => $user->username,
            'name' => $user->name,
            'color' => $this->getColor($user->investment_count ?? 0),
        ];

        $children = User::where('placement_id', $user->id)->get();

        $left = $children->where('position', 'left')->first();
        $right = $children->where('position', 'right')->first();

        if ($left) {
            $this->buildLevels($left->id, $level + 1, $levels);
        } else {
            $levels[$level + 1][] = ['blank' => true];
        }

        if ($right) {
            $this->buildLevels($right->id, $level + 1, $levels);
        } else {
            $levels[$level + 1][] = ['blank' => true];
        }
    }

    // public function index()
    // {
    //     $user = Auth::user();

    //     // recursive tree building
    //     $tree = $this->buildTree($user->id);

    //     return view('team_tree', compact('user', 'tree'));
    // }

    // private function buildTree($userId, $depth = 3)
    // {
    //     if ($depth == 0) return [];

    //     $user = User::with('leftChild', 'rightChild')->find($userId);

    //     if (!$user) return [];

    //     return [
    //         'id' => $user->id,
    //         'username' => $user->username,
    //         'name' => $user->name,
    //         'investment_count' => $user->investment_count ?? 0,
    //         'color' => $this->getColor($user->investment_count ?? 0),
    //         'left' => $user->leftChild ? $this->buildTree($user->leftChild->id, $depth - 1) : null,
    //         'right' => $user->rightChild ? $this->buildTree($user->rightChild->id, $depth - 1) : null,
    //     ];
    // }

    private function getColor($count)
    {
        return match (true) {
            $count == 0 => 'red',
            $count == 1 => 'green',
            $count == 2 => 'green',
            $count >= 3 => 'green',
        };
    }

    public function directReferral()
    {
        $userId = Auth::id();

        $directs = User::where('sponsor_id', $userId)->orderBy('created_at', 'desc')->get();

        return view('team.direct-referral', compact('directs'));
    }

    public function totalDownline()
    {
        $userId = Auth::id();
        $downlineIds = [];

        $this->collectDownline($userId, $downlineIds);

        $users = User::whereIn('id', $downlineIds)->get();

        return view('team.total-downline', compact('users'));
    }

    private function collectDownline($userId, &$downlineIds)
    {
        $children = User::where('placement_id', $userId)->get();

        foreach ($children as $child) {
            $downlineIds[] = $child->id;
            $this->collectDownline($child->id, $downlineIds);
        }
    }

    public function teamLevelDownline()
    {
        $userId = Auth::id();
        $levels = [];

        $this->buildLevelss($userId, 1, $levels);

        return view('team.team-level', compact('levels'));
    }

    private function buildLevelss($userId, $level, &$levels)
    {
        $children = User::where('placement_id', $userId)->get();

        foreach ($children as $child) {
            $levels[$level][] = $child;
            $this->buildLevels($child->id, $level + 1, $levels);
        }
    }
}
