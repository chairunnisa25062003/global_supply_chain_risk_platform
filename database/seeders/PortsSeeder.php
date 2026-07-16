<?php

namespace Database\Seeders;

use App\Models\Port;
use Illuminate\Database\Seeder;

class PortsSeeder extends Seeder
{

    public function run(): void
    {
        $ports = [
            ['name' => 'Port of Singapore', 'country' => 'Singapore', 'unlocode' => 'SGSIN', 'latitude' => 1.2644, 'longitude' => 103.8200, 'harbor_size' => 'Large'],
            ['name' => 'Port of Shanghai', 'country' => 'China', 'unlocode' => 'CNSHA', 'latitude' => 31.3496, 'longitude' => 121.5030, 'harbor_size' => 'Large'],
            ['name' => 'Port of Ningbo-Zhoushan', 'country' => 'China', 'unlocode' => 'CNNGB', 'latitude' => 29.8683, 'longitude' => 121.5440, 'harbor_size' => 'Large'],
            ['name' => 'Port of Shenzhen', 'country' => 'China', 'unlocode' => 'CNSZX', 'latitude' => 22.5431, 'longitude' => 114.0579, 'harbor_size' => 'Large'],
            ['name' => 'Port of Guangzhou', 'country' => 'China', 'unlocode' => 'CNGZG', 'latitude' => 23.1030, 'longitude' => 113.2600, 'harbor_size' => 'Large'],
            ['name' => 'Port of Qingdao', 'country' => 'China', 'unlocode' => 'CNTAO', 'latitude' => 36.0671, 'longitude' => 120.3826, 'harbor_size' => 'Large'],
            ['name' => 'Port of Tianjin', 'country' => 'China', 'unlocode' => 'CNTSN', 'latitude' => 38.9858, 'longitude' => 117.7128, 'harbor_size' => 'Large'],
            ['name' => 'Port of Busan', 'country' => 'South Korea', 'unlocode' => 'KRPUS', 'latitude' => 35.0951, 'longitude' => 129.0756, 'harbor_size' => 'Large'],
            ['name' => 'Port of Hong Kong', 'country' => 'Hong Kong', 'unlocode' => 'HKHKG', 'latitude' => 22.2908, 'longitude' => 114.1501, 'harbor_size' => 'Large'],
            ['name' => 'Port of Kaohsiung', 'country' => 'Taiwan', 'unlocode' => 'TWKHH', 'latitude' => 22.6163, 'longitude' => 120.2934, 'harbor_size' => 'Medium'],
            ['name' => 'Port Klang', 'country' => 'Malaysia', 'unlocode' => 'MYPKG', 'latitude' => 3.0044, 'longitude' => 101.3930, 'harbor_size' => 'Large'],
            ['name' => 'Tanjung Pelepas Port', 'country' => 'Malaysia', 'unlocode' => 'MYTPP', 'latitude' => 1.3626, 'longitude' => 103.5500, 'harbor_size' => 'Large'],
            ['name' => 'Tanjung Priok Port', 'country' => 'Indonesia', 'unlocode' => 'IDJKT', 'latitude' => -6.1046, 'longitude' => 106.8804, 'harbor_size' => 'Large'],
            ['name' => 'Tanjung Perak Port', 'country' => 'Indonesia', 'unlocode' => 'IDSUB', 'latitude' => -7.1978, 'longitude' => 112.7342, 'harbor_size' => 'Medium'],
            ['name' => 'Laem Chabang Port', 'country' => 'Thailand', 'unlocode' => 'THLCH', 'latitude' => 13.0827, 'longitude' => 100.8833, 'harbor_size' => 'Large'],
            ['name' => 'Port of Ho Chi Minh (Cat Lai)', 'country' => 'Vietnam', 'unlocode' => 'VNSGN', 'latitude' => 10.7647, 'longitude' => 106.7580, 'harbor_size' => 'Medium'],
            ['name' => 'Manila South Harbor', 'country' => 'Philippines', 'unlocode' => 'PHMNL', 'latitude' => 14.5832, 'longitude' => 120.9578, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Colombo', 'country' => 'Sri Lanka', 'unlocode' => 'LKCMB', 'latitude' => 6.9497, 'longitude' => 79.8420, 'harbor_size' => 'Large'],
            ['name' => 'Jawaharlal Nehru Port (JNPT)', 'country' => 'India', 'unlocode' => 'INNSA', 'latitude' => 18.9490, 'longitude' => 72.9525, 'harbor_size' => 'Large'],
            ['name' => 'Chennai Port', 'country' => 'India', 'unlocode' => 'INMAA', 'latitude' => 13.0944, 'longitude' => 80.2925, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Karachi', 'country' => 'Pakistan', 'unlocode' => 'PKKHI', 'latitude' => 24.8467, 'longitude' => 66.9836, 'harbor_size' => 'Large'],
            ['name' => 'Jebel Ali Port', 'country' => 'United Arab Emirates', 'unlocode' => 'AEJEA', 'latitude' => 25.0119, 'longitude' => 55.0617, 'harbor_size' => 'Large'],
            ['name' => 'Port of Alexandria', 'country' => 'Egypt', 'unlocode' => 'EGALY', 'latitude' => 31.2001, 'longitude' => 29.9187, 'harbor_size' => 'Medium'],
            ['name' => 'Port Said', 'country' => 'Egypt', 'unlocode' => 'EGPSD', 'latitude' => 31.2653, 'longitude' => 32.3019, 'harbor_size' => 'Large'],
            ['name' => 'Port of Durban', 'country' => 'South Africa', 'unlocode' => 'ZADUR', 'latitude' => -29.8587, 'longitude' => 31.0218, 'harbor_size' => 'Large'],
            ['name' => 'Port of Lagos (Apapa)', 'country' => 'Nigeria', 'unlocode' => 'NGLOS', 'latitude' => 6.4531, 'longitude' => 3.3958, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Rotterdam', 'country' => 'Netherlands', 'unlocode' => 'NLRTM', 'latitude' => 51.9497, 'longitude' => 4.1428, 'harbor_size' => 'Large'],
            ['name' => 'Port of Antwerp-Bruges', 'country' => 'Belgium', 'unlocode' => 'BEANR', 'latitude' => 51.2895, 'longitude' => 4.3357, 'harbor_size' => 'Large'],
            ['name' => 'Port of Hamburg', 'country' => 'Germany', 'unlocode' => 'DEHAM', 'latitude' => 53.5461, 'longitude' => 9.9661, 'harbor_size' => 'Large'],
            ['name' => 'Port of Bremerhaven', 'country' => 'Germany', 'unlocode' => 'DEBRV', 'latitude' => 53.5396, 'longitude' => 8.5809, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Le Havre', 'country' => 'France', 'unlocode' => 'FRLEH', 'latitude' => 49.4938, 'longitude' => 0.1077, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Felixstowe', 'country' => 'United Kingdom', 'unlocode' => 'GBFXT', 'latitude' => 51.9539, 'longitude' => 1.3512, 'harbor_size' => 'Large'],
            ['name' => 'Port of Valencia', 'country' => 'Spain', 'unlocode' => 'ESVLC', 'latitude' => 39.4425, 'longitude' => -0.3222, 'harbor_size' => 'Large'],
            ['name' => 'Port of Algeciras', 'country' => 'Spain', 'unlocode' => 'ESALG', 'latitude' => 36.1408, 'longitude' => -5.4526, 'harbor_size' => 'Large'],
            ['name' => 'Port of Genoa', 'country' => 'Italy', 'unlocode' => 'ITGOA', 'latitude' => 44.4056, 'longitude' => 8.9463, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Piraeus', 'country' => 'Greece', 'unlocode' => 'GRPIR', 'latitude' => 37.9475, 'longitude' => 23.6367, 'harbor_size' => 'Large'],
            ['name' => 'Port of Gdansk', 'country' => 'Poland', 'unlocode' => 'PLGDN', 'latitude' => 54.3620, 'longitude' => 18.6466, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Tokyo', 'country' => 'Japan', 'unlocode' => 'JPTYO', 'latitude' => 35.6252, 'longitude' => 139.7752, 'harbor_size' => 'Large'],
            ['name' => 'Port of Yokohama', 'country' => 'Japan', 'unlocode' => 'JPYOK', 'latitude' => 35.4437, 'longitude' => 139.6380, 'harbor_size' => 'Large'],
            ['name' => 'Port of Nagoya', 'country' => 'Japan', 'unlocode' => 'JPNGO', 'latitude' => 35.0833, 'longitude' => 136.8833, 'harbor_size' => 'Large'],
            ['name' => 'Port of Los Angeles', 'country' => 'United States', 'unlocode' => 'USLAX', 'latitude' => 33.7395, 'longitude' => -118.2610, 'harbor_size' => 'Large'],
            ['name' => 'Port of Long Beach', 'country' => 'United States', 'unlocode' => 'USLGB', 'latitude' => 33.7550, 'longitude' => -118.2160, 'harbor_size' => 'Large'],
            ['name' => 'Port of New York and New Jersey', 'country' => 'United States', 'unlocode' => 'USNYC', 'latitude' => 40.6700, 'longitude' => -74.0700, 'harbor_size' => 'Large'],
            ['name' => 'Port of Savannah', 'country' => 'United States', 'unlocode' => 'USSAV', 'latitude' => 32.0835, 'longitude' => -81.0998, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Vancouver', 'country' => 'Canada', 'unlocode' => 'CAVAN', 'latitude' => 49.2827, 'longitude' => -123.1207, 'harbor_size' => 'Large'],
            ['name' => 'Port of Santos', 'country' => 'Brazil', 'unlocode' => 'BRSSZ', 'latitude' => -23.9608, 'longitude' => -46.3339, 'harbor_size' => 'Large'],
            ['name' => 'Port of Buenos Aires', 'country' => 'Argentina', 'unlocode' => 'ARBUE', 'latitude' => -34.6037, 'longitude' => -58.3697, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Sydney (Botany Bay)', 'country' => 'Australia', 'unlocode' => 'AUSYD', 'latitude' => -33.9500, 'longitude' => 151.2260, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Melbourne', 'country' => 'Australia', 'unlocode' => 'AUMEL', 'latitude' => -37.8136, 'longitude' => 144.9270, 'harbor_size' => 'Large'],
        ];

        foreach ($ports as $port) {
            Port::firstOrCreate(
                ['unlocode' => $port['unlocode']],
                $port
            );
        }
    }
}
