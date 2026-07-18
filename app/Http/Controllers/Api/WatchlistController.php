<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Watchlist;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;


class WatchlistController extends Controller
{
    
    public function index(): JsonResponse
    {
        $items = Watchlist::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->get();

        return response()->json($items);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'country_name' => ['required', 'string', 'max:255'],
        ]);

    
        $item = Watchlist::firstOrCreate([
            'user_id'      => auth()->id(),
            'country_name' => $validated['country_name'],
        ]);

        return response()->json($item, 201);
    }


    public function destroy(int $id): JsonResponse
    {
        $item = Watchlist::where('user_id', auth()->id())
            ->where('id', $id)
            ->first();

        if (! $item) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        $item->delete();

        return response()->json(['message' => 'Berhasil dihapus dari watchlist.']);
    }
}
