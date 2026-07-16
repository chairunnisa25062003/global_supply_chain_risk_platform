<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Port;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PortController extends Controller
{
   
    public function index(Request $request): JsonResponse
    {
        $query = Port::query();

        if ($search = $request->query('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($country = $request->query('country')) {
            $query->where('country', 'like', "%{$country}%");
        }

        $ports = $query->orderBy('name')->get();

        return response()->json($ports);
    }
}
