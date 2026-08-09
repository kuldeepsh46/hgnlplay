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
        // $user = auth()->user();

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
    // private function buildTree($userIdOrMemberId)
    // {
    //     // 1. Updated Search Logic: Find user by member_id OR internal id
    //     $user = User::with('orders.package')->where('member_id', $userIdOrMemberId)->orWhere('id', $userIdOrMemberId)->first();

    //     if (!$user) {
    //         return [
    //             'id' => null,
    //             'member_id' => null,
    //             'total_business' => 0,
    //             'total_contributors' => 0,
    //         ];
    //     }

    //     // 2. Calculate this specific user's investment
    //     $personalInvestment = $user->orders->sum(function ($order) {
    //         // Updated to use order amount as we discussed earlier for accuracy
    //         return $order->amount ?? 0;
    //     });

    //     // 3. Get Children Data (Recursive calls stay the same)
    //     $leftUser = User::where('placement_id', $user->id)->where('position', 'left')->first();
    //     $rightUser = User::where('placement_id', $user->id)->where('position', 'right')->first();

    //     $leftNode = $leftUser ? $this->buildTree($leftUser->id) : null;
    //     $rightNode = $rightUser ? $this->buildTree($rightUser->id) : null;

    //     // 4. Calculate Totals for THIS node based on children
    //     $leftBranchTotal = $leftNode ? $leftNode['personal_investment'] + $leftNode['total_business'] : 0;
    //     $rightBranchTotal = $rightNode ? $rightNode['personal_investment'] + $rightNode['total_business'] : 0;

    //     $leftBranchContributors = $leftNode ? $leftNode['is_contributor'] + $leftNode['total_contributors'] : 0;
    //     $rightBranchContributors = $rightNode ? $rightNode['is_contributor'] + $rightNode['total_contributors'] : 0;
    //     return [
    //         'id' => $user->id,
    //         'member_id' => $user->member_id,
    //         'username' => $user->username,
    //         'personal_investment' => $personalInvestment,
    //         'is_active' => $personalInvestment > 0,
    //         'is_contributor' => $personalInvestment > 0 ? 1 : 0,

    //         'total_business_left' => $leftBranchTotal,
    //         'total_contributors_left' => $leftBranchContributors,
    //         'total_business_right' => $rightBranchTotal,
    //         'total_contributors_right' => $rightBranchContributors,

    //         // NEW NODE COUNTS
    //         'left_count' => $leftNode ? 1 + ($leftNode['total_count'] ?? 0) : 0,
    //         'right_count' => $rightNode ? 1 + ($rightNode['total_count'] ?? 0) : 0,
    //         'total_count' => ($leftNode ? 1 + ($leftNode['total_count'] ?? 0) : 0) + ($rightNode ? 1 + ($rightNode['total_count'] ?? 0) : 0),

    //         'left' => $leftNode,
    //         'right' => $rightNode,

    //         'total_business' => $leftBranchTotal + $rightBranchTotal,
    //         'total_contributors' => $leftBranchContributors + $rightBranchContributors,
    //     ];
    // }
    private function buildTree($userIdOrMemberId)
{
    // 1. Find user by member_id OR internal id
    $user = User::with('orders.package')->where('member_id', $userIdOrMemberId)->orWhere('id', $userIdOrMemberId)->first();

    if (!$user) {
        return [
            'id' => null,
            'member_id' => null,
            'total_business' => 0,
            'total_contributors' => 0,
        ];
    }

    // 2. Split this user's own orders into first purchase vs renewals.
    // First purchase = the order with the lowest id for this user (matches
    // the "renewal" definition already used elsewhere in the controller,
    // where an order is a renewal if an earlier-id order exists for the user).
    $sortedOrders = $user->orders->sortBy('id')->values();
    $firstOrder = $sortedOrders->first();

    $firstPurchaseAmount = $firstOrder ? ($firstOrder->amount ?? 0) : 0;
    $renewalAmount = $sortedOrders->slice(1)->sum(fn($order) => $order->amount ?? 0);

    // Kept for backward compatibility with anything already reading this key
    $personalInvestment = $firstPurchaseAmount + $renewalAmount;

    // 3. Get Children Data (Recursive calls stay the same)
    $leftUser = User::where('placement_id', $user->id)->where('position', 'left')->first();
    $rightUser = User::where('placement_id', $user->id)->where('position', 'right')->first();

    $leftNode = $leftUser ? $this->buildTree($leftUser->id) : null;
    $rightNode = $rightUser ? $this->buildTree($rightUser->id) : null;

    // 4. Calculate Totals for THIS node based on children
    $leftBranchTotal = $leftNode ? $leftNode['personal_investment'] + $leftNode['total_business'] : 0;
    $rightBranchTotal = $rightNode ? $rightNode['personal_investment'] + $rightNode['total_business'] : 0;

    $leftBranchContributors = $leftNode ? $leftNode['is_contributor'] + $leftNode['total_contributors'] : 0;
    $rightBranchContributors = $rightNode ? $rightNode['is_contributor'] + $rightNode['total_contributors'] : 0;

    // 4b. Same aggregation pattern, split by first-purchase vs renewal
    $leftBranchFirstPurchase = $leftNode ? $leftNode['personal_first_purchase'] + $leftNode['total_first_purchase'] : 0;
    $leftBranchRenewal       = $leftNode ? $leftNode['personal_renewal'] + $leftNode['total_renewal'] : 0;

    $rightBranchFirstPurchase = $rightNode ? $rightNode['personal_first_purchase'] + $rightNode['total_first_purchase'] : 0;
    $rightBranchRenewal       = $rightNode ? $rightNode['personal_renewal'] + $rightNode['total_renewal'] : 0;

    return [
        'id' => $user->id,
        'member_id' => $user->member_id,
        'username' => $user->username,
        'personal_investment' => $personalInvestment,
        'personal_first_purchase' => $firstPurchaseAmount,
        'personal_renewal' => $renewalAmount,
        'is_active' => $personalInvestment > 0,
        'is_contributor' => $personalInvestment > 0 ? 1 : 0,

        'total_business_left' => $leftBranchTotal,
        'total_contributors_left' => $leftBranchContributors,
        'total_business_right' => $rightBranchTotal,
        'total_contributors_right' => $rightBranchContributors,

        // NEW: first-purchase / renewal split per side
        'total_first_purchase_left' => $leftBranchFirstPurchase,
        'total_renewal_left' => $leftBranchRenewal,
        'total_first_purchase_right' => $rightBranchFirstPurchase,
        'total_renewal_right' => $rightBranchRenewal,

        // NODE COUNTS
        'left_count' => $leftNode ? 1 + ($leftNode['total_count'] ?? 0) : 0,
        'right_count' => $rightNode ? 1 + ($rightNode['total_count'] ?? 0) : 0,
        'total_count' => ($leftNode ? 1 + ($leftNode['total_count'] ?? 0) : 0) + ($rightNode ? 1 + ($rightNode['total_count'] ?? 0) : 0),

        'left' => $leftNode,
        'right' => $rightNode,

        'total_business' => $leftBranchTotal + $rightBranchTotal,
        'total_contributors' => $leftBranchContributors + $rightBranchContributors,

        // NEW: aggregated across both sides (mirrors total_business)
        'total_first_purchase' => $leftBranchFirstPurchase + $rightBranchFirstPurchase,
        'total_renewal' => $leftBranchRenewal + $rightBranchRenewal,
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

    // public function list()
    // {
    //     $user = Auth::user();

    //     // Get all downline users recursively (any depth)
    //     // $teamMembers = $user->allDescendants();
    //     // $teamMembers = $user->allDescendants()->sortBy('created_at');
    //     $teamMembers = $user->allDescendants()->reject(fn($member) => $member->id == 27)->sortBy('created_at');

    //     return view('team.list', compact('user', 'teamMembers'));
    // }

    // public function list()
    // {
    //     $user = Auth::user();
    //     $allDownliners = [];

    //     // 1. Identify your two direct entry points into the tree (Level 1)
    //     $leftRoot = \App\Models\User::where('placement_id', $user->id)->where('position', 'left')->first();

    //     $rightRoot = \App\Models\User::where('placement_id', $user->id)->where('position', 'right')->first();

    //     // 2. Explore the Left Subtree - Force every descendant to be labeled 'left'
    //     if ($leftRoot) {
    //         $this->crawlAndLabel($leftRoot, 'left', $allDownliners);
    //     }

    //     // 3. Explore the Right Subtree - Force every descendant to be labeled 'right'
    //     if ($rightRoot) {
    //         $this->crawlAndLabel($rightRoot, 'right', $allDownliners);
    //     }

    //     // 4. Convert to collection, apply your specific filter (reject ID 27), and sort
    //     $teamMembers = collect($allDownliners)->reject(fn($member) => $member->id == 27)->sortBy('created_at');

    //     return view('team.list', compact('user', 'teamMembers'));
    // }
    // public function list()
    // {
    //     $user = \Illuminate\Support\Facades\Auth::user();
    //     $allDownliners = [];

    //     // 1. MANUALLY FIND THE TWO GATEKEEPERS
    //     $leftBranchRoot = \App\Models\User::where('placement_id', $user->id)->where('position', 'left')->first();

    //     $rightBranchRoot = \App\Models\User::where('placement_id', $user->id)->where('position', 'right')->first();

    //     // 2. FORCE THE LEFT SIDE
    //     if ($leftRoot = $leftBranchRoot) {
    //         $this->crawlAndForceSide($leftRoot, 'left', $allDownliners);
    //     }

    //     // 3. FORCE THE RIGHT SIDE
    //     if ($rightRoot = $rightBranchRoot) {
    //         $this->crawlAndForceSide($rightRoot, 'right', $allDownliners);
    //     }

    //     // 4. PREPARE THE COLLECTION
    //     $teamMembers = collect($allDownliners)->reject(fn($m) => $m->id == 27)->sortBy('created_at');

    //     return view('team.list', compact('user', 'teamMembers'));
    // }

    // public function list()
    // {
    //     $user = \Illuminate\Support\Facades\Auth::user();
    //     $allDownliners = [];

    //     // 1. GATHER ALL DOWNLINE USERS
    //     // Changed to 'sponsor_id' and 'get()' to match your database query results
    //     $leftDirects = \App\Models\User::where('sponsor_id', $user->id)->where('position', 'left')->get();

    //     $rightDirects = \App\Models\User::where('sponsor_id', $user->id)->where('position', 'right')->get();

    //     // Loop through all left direct users and crawl their trees
    //     foreach ($leftDirects as $leftRoot) {
    //         $leftRoot->side = 'left';
    //         $allDownliners[] = $leftRoot;
    //         $this->crawlAndForceSide($leftRoot, 'left', $allDownliners);
    //     }

    //     // Loop through all right direct users and crawl their trees
    //     foreach ($rightDirects as $rightRoot) {
    //         $rightRoot->side = 'right';
    //         $allDownliners[] = $rightRoot;
    //         $this->crawlAndForceSide($rightRoot, 'right', $allDownliners);
    //     }

    //     // 2. EXTRACT IDS
    //     // Added unique() to prevent duplicates and replaced hardcoded '27' with $user->id
    //     $downlineIds = collect($allDownliners)->pluck('id')->unique()->filter(fn($id) => $id != $user->id)->toArray();
    //     // dd($downlineIds);

    //     // 3. FETCH DATA WITH ACTIVATION DATE & TOTAL EMIS
    //     if (empty($downlineIds)) {
    //         $teamMembers = collect([]);
    //     } else {
    //         $teamMembers = \App\Models\User::query()
    //             ->select('users.*')
    //             ->addSelect([
    //                 // First completed order date (Activation Date)
    //                 'activation_date' => \DB::table('orders')->selectRaw('MIN(created_at)')->whereColumn('user_id', 'users.id')->where('status', 'completed')->limit(1),

    //                 // Total completed orders (EMIs paid)
    //                 'total_emis_paid' => \DB::table('orders')->selectRaw('COUNT(*)')->whereColumn('user_id', 'users.id')->where('status', 'completed'),
    //             ])
    //             ->whereIn('id', $downlineIds)
    //             ->get() // 1. Fetch the data FIRST (keeps view mapping intact)
    //             ->sortBy('created_at') // 2. Sort the Collection AFTER
    //             ->values(); // 3. Reset the array keys
    //     }

    //     return view('team.list', compact('user', 'teamMembers'));
    // }

    // public function list()
    // {
    //     $user = \Illuminate\Support\Facades\Auth::user();

    //     // Build same tree used by tree page
    //     $tree = $this->buildTree($user->id);

    //     // Left side = everything under A's left root
    //     // Right side = everything under A's right root
    //     $leftIds = $this->collectTreeIds($tree['left'] ?? null);
    //     $rightIds = $this->collectTreeIds($tree['right'] ?? null);

    //     $downlineIds = array_merge($leftIds, $rightIds);

    //     if (empty($downlineIds)) {
    //         $teamMembers = collect([]);
    //     } else {
    //         $teamMembers = \App\Models\User::query()
    //             ->select('users.*')
    //             ->addSelect([
    //                 'activation_date' => \DB::table('orders')->selectRaw('MIN(created_at)')->whereColumn('user_id', 'users.id')->where('status', 'completed')->limit(1),
    //                 'total_emis_paid' => \DB::table('orders')->selectRaw('COUNT(*)')->whereColumn('user_id', 'users.id')->where('status', 'completed'),
    //             ])
    //             ->whereIn('id', $downlineIds)
    //             ->get()
    //             ->sortBy('created_at')
    //             ->values();

    //         // IMPORTANT: side is based on root branch, not user's own position
    //         $teamMembers->each(function ($member) use ($leftIds, $rightIds) {
    //             if (in_array($member->id, $leftIds)) {
    //                 $member->side = 'left';
    //             } elseif (in_array($member->id, $rightIds)) {
    //                 $member->side = 'right';
    //             }
    //         });
    //     }

    //     return view('team.list', compact('user', 'teamMembers'));
    // }

    public function list()
{
    $user = \Illuminate\Support\Facades\Auth::user();

    // Build same tree used by tree page
    $tree = $this->buildTree($user->id);

    // Left side = everything under A's left root
    // Right side = everything under A's right root
    $leftIds = $this->collectTreeIds($tree['left'] ?? null);
    $rightIds = $this->collectTreeIds($tree['right'] ?? null);

    $downlineIds = array_merge($leftIds, $rightIds);

    if (empty($downlineIds)) {
        $teamMembers = collect([]);
    } else {
        $teamMembers = \App\Models\User::query()
            ->select('users.*')
            ->addSelect([
                'activation_date' => \DB::table('orders')->selectRaw('MIN(created_at)')->whereColumn('user_id', 'users.id')->where('status', 'completed')->limit(1),
                'total_emis_paid' => \DB::table('orders')->selectRaw('COUNT(*)')->whereColumn('user_id', 'users.id')->where('status', 'completed'),
            ])
            // Eager-load each member's completed orders + the package on each,
            // same relation buildTree() already uses (orders.package), so this
            // won't N+1 query per row in the table.
            ->with(['orders' => function ($q) {
                $q->where('status', 'completed')
                  ->orderBy('created_at')
                  ->with('package:id,name'); // trim to the columns you actually need
            }])
            ->whereIn('id', $downlineIds)
            ->get()
            ->sortBy('created_at')
            ->values();

        // IMPORTANT: side is based on root branch, not user's own position
        $teamMembers->each(function ($member) use ($leftIds, $rightIds) {
            if (in_array($member->id, $leftIds)) {
                $member->side = 'left';
            } elseif (in_array($member->id, $rightIds)) {
                $member->side = 'right';
            }

            // Compact "Package ×N" summary, e.g. ["Starter ×2", "Golden"]
            $member->packages_summary = $member->orders
                ->pluck('package.name')
                ->filter() // drop any order whose package relation is missing
                ->countBy()
                ->map(fn($count, $name) => $count > 1 ? "{$name} ×{$count}" : $name)
                ->values();
        });
    }

    return view('team.list', compact('user', 'teamMembers'));
}

    private function collectTreeIds($node)
    {
        if (!$node) {
            return [];
        }

        $ids = [];

        if (!empty($node['id'])) {
            $ids[] = $node['id'];
        }

        $ids = array_merge($ids, $this->collectTreeIds($node['left'] ?? null));
        $ids = array_merge($ids, $this->collectTreeIds($node['right'] ?? null));

        return $ids;
    }

    /**
     * STRICT CRAWLER
     */
    private function crawlAndForceSide($node, $side, &$list)
    {
        // We overwrite the 'position' property in the object memory
        // so the Blade sees 'left' or 'right' regardless of the DB value.
        $node->position = $side;
        $list[] = $node;

        // Get EVERY child of this node
        $children = \App\Models\User::where('placement_id', $node->id)->get();

        foreach ($children as $child) {
            // We pass the parent's $side (the forced one) to the child
            $this->crawlAndForceSide($child, $side, $list);
        }
    }
    /**
     * Recursive helper to ensure descendants inherit the branch side
     */
    private function crawlAndLabel($node, $side, &$list)
    {
        // We attach a dynamic property 'team_side' to the object
        // This is what your Blade should use: {{ $member->team_side }}
        $node->team_side = $side;
        $list[] = $node;

        // Find children where this node is the placement_id
        $children = \App\Models\User::where('placement_id', $node->id)->get();

        foreach ($children as $child) {
            // Crucial: We pass the SAME $side down.
            // A 'left' child of a 'right' parent remains 'right' for the root user.
            $this->crawlAndLabel($child, $side, $list);
        }
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

    // public function totalDownline()
    // {
    //     $userId = Auth::id();
    //     $downlineIds = [];

    //     $this->collectDownline($userId, $downlineIds);

    //     $users = User::whereIn('id', $downlineIds)->get();

    //     return view('team.total-downline', compact('users'));
    // }
    public function totalDownline()
    {
        $user = Auth::user();
        $allDownliners = [];

        // 1. Identify the roots of YOUR two main branches
        $leftBranchRoot = User::where('placement_id', $user->id)->where('position', 'left')->first();

        $rightBranchRoot = User::where('placement_id', $user->id)->where('position', 'right')->first();

        // 2. Explore the Left side (Force 'left' tag)
        if ($leftBranchRoot) {
            $this->crawlAndTag($leftBranchRoot, 'left', $allDownliners);
        }

        // 3. Explore the Right side (Force 'right' tag)
        if ($rightBranchRoot) {
            $this->crawlAndTag($rightBranchRoot, 'right', $allDownliners);
        }

        // 4. Convert to collection for the view
        $users = collect($allDownliners);

        return view('team.total-downline', compact('users'));
    }

    /**
     * Recursive helper to assign the branch side relative to YOU
     */
    private function crawlAndTag($node, $side, &$list)
    {
        // We set a dynamic property 'team_side' so the blade
        // knows which branch they are in relative to YOU.
        $node->team_side = $side;
        $list[] = $node;

        $children = User::where('placement_id', $node->id)->get();

        foreach ($children as $child) {
            $this->crawlAndTag($child, $side, $list);
        }
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
