<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Port;
use App\Models\ActivityLog;
use Illuminate\Http\Request;


class PortImportController extends Controller
{
    
    private const REQUIRED_COLUMNS = ['name', 'country', 'latitude', 'longitude'];

    public function store(Request $request)
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'], // maks 10MB
        ]);

        $path = $request->file('csv_file')->getRealPath();
        $handle = fopen($path, 'r');

        if (! $handle) {
            return back()->with('error', 'Gagal membuka file CSV.');
        }

       
        $header = array_map(fn ($h) => strtolower(trim($h)), fgetcsv($handle));

       
        $missingColumns = array_diff(self::REQUIRED_COLUMNS, $header);
        if (! empty($missingColumns)) {
            fclose($handle);
            return back()->with('error',
                'Kolom wajib tidak ditemukan di CSV: ' . implode(', ', $missingColumns) .
                '. Pastikan file punya kolom: name, country, latitude, longitude (nama kolom lain boleh ada, akan diabaikan).'
            );
        }

        $imported = 0;
        $skipped = 0;

        while (($row = fgetcsv($handle)) !== false) {
         
            $data = array_combine($header, $row);

            $name = trim($data['name'] ?? '');
            $country = trim($data['country'] ?? '');
            $lat = is_numeric($data['latitude'] ?? null) ? (float) $data['latitude'] : null;
            $lon = is_numeric($data['longitude'] ?? null) ? (float) $data['longitude'] : null;

          
            $isValidCoordinate = $lat !== null && $lon !== null
                && $lat >= -90 && $lat <= 90
                && $lon >= -180 && $lon <= 180;

            if (empty($name) || empty($country) || ! $isValidCoordinate) {
                $skipped++;
                continue;
            }

            Port::updateOrCreate(
                ['name' => $name, 'country' => $country],
                [
                    'unlocode'    => $data['unlocode'] ?? $data['locode'] ?? null,
                    'latitude'    => $lat,
                    'longitude'   => $lon,
                    'harbor_size' => $data['harbor_size'] ?? $data['size'] ?? 'Medium',
                    'status'      => $data['status'] ?? 'Normal',
                ]
            );

            $imported++;
        }

        fclose($handle);

        ActivityLog::record('import_ports', "Import CSV: {$imported} pelabuhan berhasil, {$skipped} baris dilewati");

        return back()->with('success', "Import selesai: {$imported} pelabuhan berhasil disimpan, {$skipped} baris dilewati (data tidak lengkap).");
    }
}
