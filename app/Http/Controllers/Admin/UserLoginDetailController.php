<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserLoginDetail;
use App\Models\User;
use Illuminate\Http\Request;

class UserLoginDetailController extends Controller
{
    public function index(Request $request)
    {
        // Seed dummy data if table is empty
        if (UserLoginDetail::count() === 0) {
            $this->seedDummyData();
        }

        $query = UserLoginDetail::with('user')->orderBy('login_at', 'desc');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('login_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('login_at', '<=', $request->date_to);
        }

        $loginDetails = $query->paginate(20);
        $users = User::orderBy('name')->get();

        return view('acp.system.user-login-details.index', compact('loginDetails', 'users'));
    }

    private function seedDummyData()
    {
        $users = User::take(5)->get();
        $statuses = ['success', 'success', 'success', 'failed'];
        $agents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Safari/605.1',
            'Mozilla/5.0 (X11; Linux x86_64) Firefox/121.0',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0) Safari/605.1',
        ];

        foreach ($users as $user) {
            for ($i = 0; $i < rand(5, 15); $i++) {
                $loginAt = now()->subDays(rand(0, 30))->subHours(rand(0, 23));
                UserLoginDetail::create([
                    'user_id' => $user->id,
                    'login_at' => $loginAt,
                    'logout_at' => rand(0, 1) ? $loginAt->addHours(rand(1, 8)) : null,
                    'ip_address' => rand(1, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(1, 254),
                    'user_agent' => $agents[array_rand($agents)],
                    'status' => $statuses[array_rand($statuses)],
                ]);
            }
        }
    }
}
