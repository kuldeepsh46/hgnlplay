<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MailboxController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $queries = DB::table('queries')
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->paginate(5);

        return view('mailbox', compact('user', 'queries'));
    }

    public function store(Request $r)
    {
        $r->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        DB::table('queries')->insert([
            'user_id' => Auth::id(),
            'subject' => $r->subject,
            'message' => $r->message,
            'status' => 'Yet to review',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Your query has been sent successfully!');
    }

    // optional for AJAX later
    public function getReply($id)
    {
        $query = DB::table('queries')->find($id);
        return response()->json($query);
    }
}
