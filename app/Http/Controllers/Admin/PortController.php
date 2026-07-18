<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Port;
use Illuminate\Http\Request;

class PortController extends Controller
{
    public function index()
    {
        $ports = Port::orderBy('name')->get();

        return view('admin.ports', compact('ports'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'country'     => ['required', 'string', 'max:255'],
            'unlocode'    => ['nullable', 'string', 'max:10'],
            'latitude'    => ['required', 'numeric', 'between:-90,90'],
            'longitude'   => ['required', 'numeric', 'between:-180,180'],
            'harbor_size' => ['nullable', 'string', 'in:Small,Medium,Large'],
        ]);

        Port::create($validated);

        return back()->with('success', 'Pelabuhan baru berhasil ditambahkan.');
    }

    public function destroy(Port $port)
    {
        $port->delete();

        return back()->with('success', 'Pelabuhan berhasil dihapus.');
    }
}
