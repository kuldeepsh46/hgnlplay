<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupportController extends Controller
{
    public function index()
    {
        $all = DB::table('queries')
            ->join('users', 'queries.user_id', '=', 'users.id')
            ->select('queries.*', 'users.username', 'users.email')
            ->orderByDesc('queries.id')
            ->get();

        $pending = $all->where('status', 'Yet to review');
        $responded = $all->where('status', 'Responded');

        return view('admin.support', compact('all', 'pending', 'responded'));
    }

    public function reply(Request $r, $id)
    {
        $query = DB::table('queries')->where('id', $id)->first();
        if (!$query) {
            return back()->with('error', 'Query not found.');
        }

        // ✅ if "Yet to review" → update to "Responded"
        $status = $query->status == 'Yet to review' ? 'Responded' : $query->status;

        DB::table('queries')->where('id', $id)->update([
            'admin_reply' => $r->admin_reply,
            'status' => $status,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Reply sent successfully!');
    }

    public function getMessage($id)
    {
        $query = DB::table('queries')->where('id', $id)->first();
        return response()->json($query);
    }
}
