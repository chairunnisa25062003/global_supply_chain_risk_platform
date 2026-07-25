<?php

namespace Database\Seeders;

use App\Models\Port;
use Illuminate\Database\Seeder;


class PortStatusSeeder extends Seeder
{
    public function run(): void
    {
        $busyPorts = ['SGSIN', 'HKHKG', 'AEJEA', 'NLRTM', 'KRPUS'];
        $congestedPorts = ['CNSHA', 'USLAX', 'USLGB', 'INNSA'];

        Port::whereIn('unlocode', $busyPorts)->update(['status' => 'Busy']);
        Port::whereIn('unlocode', $congestedPorts)->update(['status' => 'Congested']);
    }
}
