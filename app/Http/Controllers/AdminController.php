<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\SocialMessage;
use App\Models\User;
use App\Models\UserActivity;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Display the administrator dashboard.
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        if (! $user || $user->role !== 'admin') {
            abort(403);
        }

        $totalUsers = User::count();
        $activeUsers = User::where('last_seen', '>=', now()->subMinutes(10))->count();
        $totalPosts = Post::count();
        $totalComments = PostComment::count();
        $totalShares = Post::sum('shares_count');
        $totalMessages = SocialMessage::count();
        $totalGroups = Group::count();

        $usersByFiliere = User::select('filiere', DB::raw('count(*) as total'))
            ->groupBy('filiere')
            ->orderByDesc('total')
            ->get();

        $topUsers = User::withCount('activities')
            ->orderByDesc('activities_count')
            ->take(5)
            ->get();

        $latestActivities = UserActivity::with('user')
            ->latest()
            ->take(20)
            ->get();

        $activityChart = UserActivity::selectRaw('DATE(created_at) as date, count(*) as total')
            ->where('created_at', '>=', now()->subDays(14))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        ActivityLogger::log($user, 'admin.dashboard.view', 'Tableau de bord administrateur consulté', []);

        return view('admin.dashboard', compact(
            'user',
            'totalUsers',
            'activeUsers',
            'totalPosts',
            'totalComments',
            'totalShares',
            'totalMessages',
            'totalGroups',
            'usersByFiliere',
            'topUsers',
            'latestActivities',
            'activityChart'
        ));
    }

    public function stats(Request $request)
    {
        $user = Auth::user();

        if (! $user || $user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $activityChart = UserActivity::selectRaw('DATE(created_at) as date, count(*) as total')
            ->where('created_at', '>=', now()->subDays(14))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'totalUsers' => User::count(),
            'activeUsers' => User::where('last_seen', '>=', now()->subMinutes(10))->count(),
            'totalPosts' => Post::count(),
            'totalComments' => PostComment::count(),
            'totalShares' => Post::sum('shares_count'),
            'totalMessages' => SocialMessage::count(),
            'totalGroups' => Group::count(),
            'activityChart' => $activityChart,
        ]);
    }
}
