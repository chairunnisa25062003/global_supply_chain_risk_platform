<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Port;
use App\Models\Article;
use App\Models\Watchlist;

class DashboardController extends Controller
{
    public function index()
    {
        
        $stats = [
            'total_users'      => User::count(),
            'total_ports'      => Port::count(),
            'total_articles'   => Article::count(),
            'total_watchlists' => Watchlist::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
