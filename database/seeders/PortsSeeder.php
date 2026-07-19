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

            // --- TAMBAHAN BARU (~50 pelabuhan) ---
            // Timur Tengah & Afrika Utara
            ['name' => 'Port of Dammam (King Abdulaziz)', 'country' => 'Saudi Arabia', 'unlocode' => 'SADMM', 'latitude' => 26.4207, 'longitude' => 50.1063, 'harbor_size' => 'Large'],
            ['name' => 'Jeddah Islamic Port', 'country' => 'Saudi Arabia', 'unlocode' => 'SAJED', 'latitude' => 21.4858, 'longitude' => 39.1925, 'harbor_size' => 'Large'],
            ['name' => 'Port of Salalah', 'country' => 'Oman', 'unlocode' => 'OMSLL', 'latitude' => 17.0151, 'longitude' => 54.0924, 'harbor_size' => 'Large'],
            ['name' => 'Port of Haifa', 'country' => 'Israel', 'unlocode' => 'ILHFA', 'latitude' => 32.8191, 'longitude' => 34.9983, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Casablanca', 'country' => 'Morocco', 'unlocode' => 'MACAS', 'latitude' => 33.6022, 'longitude' => -7.6187, 'harbor_size' => 'Medium'],
            ['name' => 'Tanger Med Port', 'country' => 'Morocco', 'unlocode' => 'MAPTM', 'latitude' => 35.8845, 'longitude' => -5.5015, 'harbor_size' => 'Large'],
            ['name' => 'Port of Tunis', 'country' => 'Tunisia', 'unlocode' => 'TNTUN', 'latitude' => 36.8065, 'longitude' => 10.3081, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Djibouti', 'country' => 'Djibouti', 'unlocode' => 'DJJIB', 'latitude' => 11.5883, 'longitude' => 43.1450, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Mombasa', 'country' => 'Kenya', 'unlocode' => 'KEMBA', 'latitude' => -4.0435, 'longitude' => 39.6682, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Dar es Salaam', 'country' => 'Tanzania', 'unlocode' => 'TZDAR', 'latitude' => -6.8235, 'longitude' => 39.2695, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Abidjan', 'country' => 'Ivory Coast', 'unlocode' => 'CIABJ', 'latitude' => 5.2893, 'longitude' => -4.0159, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Tema', 'country' => 'Ghana', 'unlocode' => 'GHTEM', 'latitude' => 5.6367, 'longitude' => 0.0093, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Luanda', 'country' => 'Angola', 'unlocode' => 'AOLAD', 'latitude' => -8.8147, 'longitude' => 13.2302, 'harbor_size' => 'Small'],
            ['name' => 'Walvis Bay Port', 'country' => 'Namibia', 'unlocode' => 'NAWVB', 'latitude' => -22.9576, 'longitude' => 14.5052, 'harbor_size' => 'Small'],
            ['name' => 'Port of Cape Town', 'country' => 'South Africa', 'unlocode' => 'ZACPT', 'latitude' => -33.9028, 'longitude' => 18.4293, 'harbor_size' => 'Medium'],
            // Amerika Selatan & Tengah
            ['name' => 'Port of Callao', 'country' => 'Peru', 'unlocode' => 'PECLL', 'latitude' => -12.0508, 'longitude' => -77.1367, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Valparaiso', 'country' => 'Chile', 'unlocode' => 'CLVAP', 'latitude' => -33.0472, 'longitude' => -71.6127, 'harbor_size' => 'Medium'],
            ['name' => 'Port of San Antonio', 'country' => 'Chile', 'unlocode' => 'CLSAI', 'latitude' => -33.5928, 'longitude' => -71.6128, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Cartagena', 'country' => 'Colombia', 'unlocode' => 'COCTG', 'latitude' => 10.3910, 'longitude' => -75.4794, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Balboa', 'country' => 'Panama', 'unlocode' => 'PABLB', 'latitude' => 8.9500, 'longitude' => -79.5667, 'harbor_size' => 'Large'],
            ['name' => 'Colon Container Terminal', 'country' => 'Panama', 'unlocode' => 'PACTB', 'latitude' => 9.3592, 'longitude' => -79.8988, 'harbor_size' => 'Large'],
            ['name' => 'Port of Manzanillo', 'country' => 'Mexico', 'unlocode' => 'MZZLO', 'latitude' => 19.0533, 'longitude' => -104.3186, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Veracruz', 'country' => 'Mexico', 'unlocode' => 'MXVER', 'latitude' => 19.1738, 'longitude' => -96.1342, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Rio de Janeiro', 'country' => 'Brazil', 'unlocode' => 'BRRIO', 'latitude' => -22.8944, 'longitude' => -43.1811, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Montevideo', 'country' => 'Uruguay', 'unlocode' => 'UYMVD', 'latitude' => -34.9011, 'longitude' => -56.2120, 'harbor_size' => 'Medium'],
            // Eropa tambahan
            ['name' => 'Port of Marseille-Fos', 'country' => 'France', 'unlocode' => 'FRMRS', 'latitude' => 43.3547, 'longitude' => 5.0913, 'harbor_size' => 'Large'],
            ['name' => 'Port of Southampton', 'country' => 'United Kingdom', 'unlocode' => 'GBSOU', 'latitude' => 50.8964, 'longitude' => -1.3935, 'harbor_size' => 'Medium'],
            ['name' => 'Port of London (Tilbury)', 'country' => 'United Kingdom', 'unlocode' => 'GBTIL', 'latitude' => 51.4667, 'longitude' => 0.3667, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Zeebrugge', 'country' => 'Belgium', 'unlocode' => 'BEZEE', 'latitude' => 51.3308, 'longitude' => 3.2050, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Wilhelmshaven', 'country' => 'Germany', 'unlocode' => 'DEWVN', 'latitude' => 53.5297, 'longitude' => 8.1114, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Gothenburg', 'country' => 'Sweden', 'unlocode' => 'SEGOT', 'latitude' => 57.7089, 'longitude' => 11.9746, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Oslo', 'country' => 'Norway', 'unlocode' => 'NOOSL', 'latitude' => 59.9139, 'longitude' => 10.7522, 'harbor_size' => 'Small'],
            ['name' => 'Port of Helsinki', 'country' => 'Finland', 'unlocode' => 'FIHEL', 'latitude' => 60.1699, 'longitude' => 24.9384, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Riga', 'country' => 'Latvia', 'unlocode' => 'LVRIX', 'latitude' => 56.9496, 'longitude' => 24.1052, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Constanta', 'country' => 'Romania', 'unlocode' => 'ROCND', 'latitude' => 44.1733, 'longitude' => 28.6383, 'harbor_size' => 'Large'],
            ['name' => 'Port of Istanbul (Ambarli)', 'country' => 'Turkey', 'unlocode' => 'TRAMB', 'latitude' => 40.9678, 'longitude' => 28.6800, 'harbor_size' => 'Large'],
            ['name' => 'Port of Trieste', 'country' => 'Italy', 'unlocode' => 'ITTRS', 'latitude' => 45.6495, 'longitude' => 13.7768, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Lisbon', 'country' => 'Portugal', 'unlocode' => 'PTLIS', 'latitude' => 38.7223, 'longitude' => -9.1393, 'harbor_size' => 'Medium'],
            // Asia tambahan
            ['name' => 'Port of Chittagong', 'country' => 'Bangladesh', 'unlocode' => 'BDCGP', 'latitude' => 22.3569, 'longitude' => 91.7832, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Yangon', 'country' => 'Myanmar', 'unlocode' => 'MMRGN', 'latitude' => 16.7967, 'longitude' => 96.1610, 'harbor_size' => 'Small'],
            ['name' => 'Port of Haiphong', 'country' => 'Vietnam', 'unlocode' => 'VNHPH', 'latitude' => 20.8449, 'longitude' => 106.6881, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Dalian', 'country' => 'China', 'unlocode' => 'CNDLC', 'latitude' => 38.9140, 'longitude' => 121.6147, 'harbor_size' => 'Large'],
            ['name' => 'Port of Xiamen', 'country' => 'China', 'unlocode' => 'CNXMN', 'latitude' => 24.4798, 'longitude' => 118.0894, 'harbor_size' => 'Large'],
            ['name' => 'Port of Incheon', 'country' => 'South Korea', 'unlocode' => 'KRINC', 'latitude' => 37.4563, 'longitude' => 126.7052, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Kobe', 'country' => 'Japan', 'unlocode' => 'JPUKB', 'latitude' => 34.6901, 'longitude' => 135.1955, 'harbor_size' => 'Large'],
            ['name' => 'Port of Osaka', 'country' => 'Japan', 'unlocode' => 'JPOSA', 'latitude' => 34.6413, 'longitude' => 135.4290, 'harbor_size' => 'Medium'],
            // Amerika Utara tambahan
            ['name' => 'Port of Houston', 'country' => 'United States', 'unlocode' => 'USHOU', 'latitude' => 29.7355, 'longitude' => -95.2716, 'harbor_size' => 'Large'],
            ['name' => 'Port of Seattle', 'country' => 'United States', 'unlocode' => 'USSEA', 'latitude' => 47.5842, 'longitude' => -122.3399, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Oakland', 'country' => 'United States', 'unlocode' => 'USOAK', 'latitude' => 37.7955, 'longitude' => -122.2778, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Charleston', 'country' => 'United States', 'unlocode' => 'USCHS', 'latitude' => 32.7833, 'longitude' => -79.9333, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Montreal', 'country' => 'Canada', 'unlocode' => 'CAMTR', 'latitude' => 45.5500, 'longitude' => -73.5500, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Halifax', 'country' => 'Canada', 'unlocode' => 'CAHAL', 'latitude' => 44.6488, 'longitude' => -63.5752, 'harbor_size' => 'Medium'],
            // Oceania tambahan
            ['name' => 'Port of Brisbane', 'country' => 'Australia', 'unlocode' => 'AUBNE', 'latitude' => -27.3820, 'longitude' => 153.1614, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Fremantle', 'country' => 'Australia', 'unlocode' => 'AUFRE', 'latitude' => -32.0569, 'longitude' => 115.7439, 'harbor_size' => 'Medium'],
            ['name' => 'Port of Auckland', 'country' => 'New Zealand', 'unlocode' => 'NZAKL', 'latitude' => -36.8485, 'longitude' => 174.7633, 'harbor_size' => 'Medium'],
        ];

        foreach ($ports as $port) {
            Port::firstOrCreate(
                ['unlocode' => $port['unlocode']],
                $port
            );
        }
    }
}
