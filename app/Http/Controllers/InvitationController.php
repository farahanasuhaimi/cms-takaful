<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\User;
use App\Services\CreditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    public function index()
    {
        $invitations = Invitation::with('inviter')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.invitations.index', compact('invitations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email|unique:invitations,email',
        ]);

        $invitation = Invitation::create([
            'email'      => $request->email,
            'token'      => Str::random(64),
            'invited_by' => auth()->id(),
            'expires_at' => now()->addDays(7),
        ]);

        $link = route('invite.show', $invitation->token);

        return back()->with('invite_link', $link)->with('success', "Invite created for {$request->email}.");
    }

    public function destroy(Invitation $invitation)
    {
        $invitation->delete();

        return back()->with('success', 'Invitation revoked.');
    }

    public function show(string $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if (! $invitation->isValid()) {
            return view('auth.invite-expired');
        }

        return view('auth.invite-register', compact('invitation'));
    }

    public function register(Request $request, string $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if (! $invitation->isValid()) {
            return redirect()->route('login')->with('status', 'This invite link has expired.');
        }

        $request->validate([
            'name'     => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $invitation->email,
            'password' => Hash::make($request->password),
        ]);

        $invitation->update(['used_at' => now()]);

        CreditService::award($user, 10, 'bonus', 'Welcome credits');

        auth()->login($user);

        return redirect()->route('dashboard')->with('success', "Welcome, {$user->name}!");
    }
}
