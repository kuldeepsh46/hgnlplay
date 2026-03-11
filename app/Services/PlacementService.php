<?php
namespace App\Services;

use App\Models\{User,BinaryNode};
use Illuminate\Support\Facades\DB;

class PlacementService
{
    /**
     * Place a user in the binary under $parent on $side (L/R).
     * If occupied, spillover to the next available node using BFS.
     */
    public function place(User $user, User $parent, string $side): BinaryNode
    {
        $side = strtoupper($side)==='R' ? 'R' : 'L';
        return DB::transaction(function() use ($user,$parent,$side) {

            // BFS queue: try target parent first, then breadth-first on that side
            $queue = [$parent->id];
            while ($queue) {
                $pid = array_shift($queue);
                $occupied = BinaryNode::where('parent_id',$pid)->where('side',$side)->first();
                if (!$occupied) {
                    return BinaryNode::create([
                        'user_id'   => $user->id,
                        'parent_id' => $pid,
                        'side'      => $side,
                    ]);
                }
                // push this child further down same side
                $queue[] = $occupied->user_id;
            }

            // Should never reach here
            throw new \RuntimeException('No placement slot found');
        });
    }
}
