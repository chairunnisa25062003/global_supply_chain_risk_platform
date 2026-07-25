<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Port;
use App\Models\ActivityLog;
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
            'status'      => ['nullable', 'string', 'in:Normal,Busy,Congested'],
        ]);

        Port::create($validated);

        ActivityLog::record('add_port', "Menambahkan pelabuhan {$validated['name']}");

        return back()->with('success', 'Pelabuhan baru berhasil ditambahkan.');
    }

    
    public function updateStatus(Request $request, Port $port)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:Normal,Busy,Congested'],
        ]);

        $port->update($validated);

        ActivityLog::record('update_port_status', "Mengubah status {$port->name} jadi {$validated['status']}");

        return back()->with('success', "Status {$port->name} diubah jadi {$validated['status']}.");
    }

    public function destroy(Port $port)
    {
        $name = $port->name;
        $port->delete();

        ActivityLog::record('delete_port', "Menghapus pelabuhan {$name}");

        return back()->with('success', 'Pelabuhan berhasil dihapus.');
    }
}
