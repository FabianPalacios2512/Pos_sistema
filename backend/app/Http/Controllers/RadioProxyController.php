<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RadioProxyController extends Controller
{
    /**
     * Buscar estaciones de radio
     * GET /api/radio/search
     */
    public function search(Request $request)
    {
        $params = $request->all();
        $cacheKey = 'radio_search_' . md5(json_encode($params));

        // Intentar obtener de caché (30 minutos)
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return response()->json($cached);
        }

        // 🚀 OPTIMIZACIÓN: Usar directamente nuestras radios locales
        // Son más rápidas y confiables que APIs externas
        $localStations = $this->getDefaultColombianStations();

        // Filtrar por estado si se proporciona
        if (isset($params['state']) && $params['state']) {
            $localStations = array_filter($localStations, function($station) use ($params) {
                return stripos($station['state'], $params['state']) !== false;
            });
            $localStations = array_values($localStations); // Reindexar
        }

        // Filtrar por nombre si se proporciona
        if (isset($params['name']) && $params['name']) {
            $localStations = array_filter($localStations, function($station) use ($params) {
                return stripos($station['name'], $params['name']) !== false;
            });
            $localStations = array_values($localStations);
        }

        // Limitar resultados
        $limit = $params['limit'] ?? 20;
        $localStations = array_slice($localStations, 0, $limit);

        // Guardar en caché por 30 minutos
        Cache::put($cacheKey, $localStations, 1800);

        return response()->json($localStations);
    }

    /**
     * Radios colombianas más famosas y escuchadas
     * URLs de las emisoras más populares de Colombia (StreamTheWorld - HTTPS)
     */
    private function getDefaultColombianStations()
    {
        return [
            // MEDELLÍN - ANTIOQUIA
            [
                'stationuuid' => 'co-medellin-1',
                'name' => 'La Mega 92.9 FM Medellín',
                'url_resolved' => 'https://playerservices.streamtheworld.com/api/livestream-redirect/LA_MEGA.mp3',
                'favicon' => 'https://www.emisorascolombianas.co/img/la-mega-medellin.jpg',
                'tags' => 'pop,rock,hits',
                'country' => 'Colombia',
                'state' => 'Antioquia',
                'votes' => 1000
            ],
            [
                'stationuuid' => 'co-medellin-2',
                'name' => 'Olímpica Stereo 104.9 FM Medellín',
                'url_resolved' => 'https://playerservices.streamtheworld.com/api/livestream-redirect/OLP_MEDELLIN.mp3',
                'favicon' => 'https://www.emisorascolombianas.co/img/olimpica-medellin.jpg',
                'tags' => 'tropical,vallenato,salsa',
                'country' => 'Colombia',
                'state' => 'Antioquia',
                'votes' => 980
            ],
            [
                'stationuuid' => 'co-medellin-3',
                'name' => 'W Radio Colombia',
                'url_resolved' => 'https://playerservices.streamtheworld.com/api/livestream-redirect/WRADIO.mp3',
                'favicon' => 'https://www.emisorascolombianas.co/img/w-radio.jpg',
                'tags' => 'noticias,talk',
                'country' => 'Colombia',
                'state' => 'Antioquia',
                'votes' => 950
            ],
            [
                'stationuuid' => 'co-medellin-4',
                'name' => 'Caracol Radio',
                'url_resolved' => 'https://playerservices.streamtheworld.com/api/livestream-redirect/CARACOL_RADIO.mp3',
                'favicon' => 'https://www.emisorascolombianas.co/img/caracol-radio.jpg',
                'tags' => 'noticias,deportes',
                'country' => 'Colombia',
                'state' => 'Antioquia',
                'votes' => 900
            ],
            [
                'stationuuid' => 'co-medellin-5',
                'name' => 'Tropicana Medellín',
                'url_resolved' => 'https://playerservices.streamtheworld.com/api/livestream-redirect/TROPICANA.mp3',
                'favicon' => 'https://www.emisorascolombianas.co/img/tropicana-medellin.jpg',
                'tags' => 'tropical,salsa',
                'country' => 'Colombia',
                'state' => 'Antioquia',
                'votes' => 850
            ],

            // BOGOTÁ
            [
                'stationuuid' => 'co-bogota-1',
                'name' => 'La Mega 90.9 FM Bogotá',
                'url_resolved' => 'https://playerservices.streamtheworld.com/api/livestream-redirect/LA_MEGA.mp3',
                'favicon' => 'https://www.emisorascolombianas.co/img/la-mega-bogota.jpg',
                'tags' => 'pop,rock,hits',
                'country' => 'Colombia',
                'state' => 'Bogota',
                'votes' => 1200
            ],
            [
                'stationuuid' => 'co-bogota-2',
                'name' => 'Olímpica Stereo 105.9 FM Bogotá',
                'url_resolved' => 'https://playerservices.streamtheworld.com/api/livestream-redirect/OLP_BOGOTA.mp3',
                'favicon' => 'https://www.emisorascolombianas.co/img/olimpica-bogota.jpg',
                'tags' => 'tropical,vallenato',
                'country' => 'Colombia',
                'state' => 'Bogota',
                'votes' => 1100
            ],
            [
                'stationuuid' => 'co-bogota-3',
                'name' => 'Radioacktiva 97.9 FM',
                'url_resolved' => 'https://playerservices.streamtheworld.com/api/livestream-redirect/RADIOACKTIVA.mp3',
                'favicon' => 'https://www.emisorascolombianas.co/img/radioacktiva.jpg',
                'tags' => 'rock,metal',
                'country' => 'Colombia',
                'state' => 'Bogota',
                'votes' => 1050
            ],
            [
                'stationuuid' => 'co-bogota-4',
                'name' => 'Tropicana Bogotá',
                'url_resolved' => 'https://playerservices.streamtheworld.com/api/livestream-redirect/TROPICANA.mp3',
                'favicon' => 'https://www.emisorascolombianas.co/img/tropicana-bogota.jpg',
                'tags' => 'tropical,salsa',
                'country' => 'Colombia',
                'state' => 'Bogota',
                'votes' => 1000
            ],

            // CALI
            [
                'stationuuid' => 'co-cali-1',
                'name' => 'Olímpica Stereo Cali',
                'url_resolved' => 'https://playerservices.streamtheworld.com/api/livestream-redirect/OLP_CALI.mp3',
                'favicon' => 'https://www.emisorascolombianas.co/img/olimpica-cali.jpg',
                'tags' => 'salsa,tropical',
                'country' => 'Colombia',
                'state' => 'Valle del Cauca',
                'votes' => 900
            ],
            [
                'stationuuid' => 'co-cali-2',
                'name' => 'Tropicana Cali',
                'url_resolved' => 'https://playerservices.streamtheworld.com/api/livestream-redirect/TROPICANA_CALI.mp3',
                'favicon' => 'https://www.emisorascolombianas.co/img/tropicana-cali.jpg',
                'tags' => 'salsa,tropical',
                'country' => 'Colombia',
                'state' => 'Valle del Cauca',
                'votes' => 880
            ],

            // BARRANQUILLA (COSTA)
            [
                'stationuuid' => 'co-quilla-1',
                'name' => 'Olímpica Stereo Barranquilla',
                'url_resolved' => 'https://playerservices.streamtheworld.com/api/livestream-redirect/OLP_BARRANQUILLA.mp3',
                'favicon' => 'https://www.emisorascolombianas.co/img/olimpica-barranquilla.jpg',
                'tags' => 'vallenato,tropical',
                'country' => 'Colombia',
                'state' => 'Atlantico',
                'votes' => 950
            ]
        ];
    }
}
