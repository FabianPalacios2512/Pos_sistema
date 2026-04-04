<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * 📻 Radio 105 FM - API Pública
 * 
 * Este controlador expone endpoints públicos para el servicio de radio
 * que cualquier aplicación externa puede consumir.
 * 
 * Documentación: https://105pos.pro/docs/radio-api
 */
class PublicRadioController extends Controller
{
    /**
     * Información del servicio de Radio
     * GET /public/radio/info
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function info()
    {
        return response()->json([
            'service' => 'Radio 105 FM',
            'version' => '1.0.0',
            'description' => 'Servicio gratuito de streaming de radio colombiana',
            'provider' => '105POS',
            'endpoints' => [
                'info' => '/api/public/radio/info',
                'stations' => '/api/public/radio/stations',
                'station' => '/api/public/radio/stations/{id}',
                'search' => '/api/public/radio/search',
                'categories' => '/api/public/radio/categories',
                'cities' => '/api/public/radio/cities',
                'widget_js' => '/api/public/radio/widget',
                'widget_css' => '/api/public/radio/styles',
            ],
            'widget_embed' => '<script src="https://105pos.pro/api/public/radio/widget"></script>',
            'documentation' => 'https://105pos.pro/docs/radio-api',
            'cors_enabled' => true,
            'rate_limit' => '100 requests per minute',
            'support' => 'soporte@105pos.pro'
        ])->header('Access-Control-Allow-Origin', '*')
          ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
          ->header('Access-Control-Allow-Headers', 'Content-Type, Accept');
    }

    /**
     * Listar todas las estaciones de radio
     * GET /public/radio/stations
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function stations(Request $request)
    {
        $limit = min($request->get('limit', 50), 100);
        $offset = max($request->get('offset', 0), 0);
        $state = $request->get('state', null);
        $category = $request->get('category', null);

        $cacheKey = "public_radio_stations_{$limit}_{$offset}_{$state}_{$category}";
        
        $data = Cache::remember($cacheKey, 3600, function () use ($limit, $offset, $state, $category) {
            $stations = $this->getAllStations();

            // Filtrar por estado/ciudad
            if ($state) {
                $stations = array_filter($stations, function($s) use ($state) {
                    return stripos($s['state'], $state) !== false || 
                           stripos($s['city'], $state) !== false;
                });
            }

            // Filtrar por categoría
            if ($category) {
                $stations = array_filter($stations, function($s) use ($category) {
                    return stripos($s['tags'], $category) !== false;
                });
            }

            $stations = array_values($stations);
            $total = count($stations);
            $stations = array_slice($stations, $offset, $limit);

            return [
                'stations' => $stations,
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data['stations'],
            'meta' => [
                'total' => $data['total'],
                'limit' => $data['limit'],
                'offset' => $data['offset'],
                'has_more' => ($data['offset'] + count($data['stations'])) < $data['total']
            ]
        ])->header('Access-Control-Allow-Origin', '*')
          ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
          ->header('Access-Control-Allow-Headers', 'Content-Type, Accept')
          ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Obtener una estación específica por ID
     * GET /public/radio/stations/{id}
     * 
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function station($id)
    {
        $stations = $this->getAllStations();
        
        $station = collect($stations)->firstWhere('id', $id);
        
        if (!$station) {
            return response()->json([
                'success' => false,
                'error' => 'Station not found',
                'message' => "No station found with ID: {$id}"
            ], 404)->header('Access-Control-Allow-Origin', '*');
        }

        return response()->json([
            'success' => true,
            'data' => $station
        ])->header('Access-Control-Allow-Origin', '*')
          ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
          ->header('Access-Control-Allow-Headers', 'Content-Type, Accept');
    }

    /**
     * Buscar estaciones de radio
     * GET /public/radio/search?q=mega
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $limit = min($request->get('limit', 20), 50);

        if (strlen($query) < 2) {
            return response()->json([
                'success' => false,
                'error' => 'Query too short',
                'message' => 'Search query must be at least 2 characters'
            ], 400)->header('Access-Control-Allow-Origin', '*');
        }

        $stations = $this->getAllStations();
        
        $results = array_filter($stations, function($s) use ($query) {
            return stripos($s['name'], $query) !== false ||
                   stripos($s['tags'], $query) !== false ||
                   stripos($s['city'], $query) !== false;
        });

        $results = array_slice(array_values($results), 0, $limit);

        return response()->json([
            'success' => true,
            'query' => $query,
            'count' => count($results),
            'data' => $results
        ])->header('Access-Control-Allow-Origin', '*')
          ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
          ->header('Access-Control-Allow-Headers', 'Content-Type, Accept');
    }

    /**
     * Listar categorías disponibles
     * GET /public/radio/categories
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function categories()
    {
        return response()->json([
            'success' => true,
            'data' => [
                ['id' => 'pop', 'name' => 'Pop & Hits', 'icon' => '🎵'],
                ['id' => 'rock', 'name' => 'Rock', 'icon' => '🎸'],
                ['id' => 'tropical', 'name' => 'Tropical & Salsa', 'icon' => '🌴'],
                ['id' => 'vallenato', 'name' => 'Vallenato', 'icon' => '🪗'],
                ['id' => 'romantica', 'name' => 'Romántica', 'icon' => '❤️'],
                ['id' => 'noticias', 'name' => 'Noticias', 'icon' => '📰'],
                ['id' => 'urbana', 'name' => 'Urbana & Reggaetón', 'icon' => '🔥'],
                ['id' => 'cristiana', 'name' => 'Cristiana', 'icon' => '✝️'],
                ['id' => 'clasica', 'name' => 'Clásica', 'icon' => '🎻'],
            ]
        ])->header('Access-Control-Allow-Origin', '*')
          ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
          ->header('Access-Control-Allow-Headers', 'Content-Type, Accept');
    }

    /**
     * Listar ciudades disponibles
     * GET /public/radio/cities
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function cities()
    {
        return response()->json([
            'success' => true,
            'data' => [
                ['id' => 'medellin', 'name' => 'Medellín', 'state' => 'Antioquia'],
                ['id' => 'bogota', 'name' => 'Bogotá', 'state' => 'Bogota'],
                ['id' => 'cali', 'name' => 'Cali', 'state' => 'Valle'],
                ['id' => 'barranquilla', 'name' => 'Barranquilla', 'state' => 'Atlantico'],
                ['id' => 'cartagena', 'name' => 'Cartagena', 'state' => 'Bolivar'],
            ]
        ])->header('Access-Control-Allow-Origin', '*')
          ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
          ->header('Access-Control-Allow-Headers', 'Content-Type, Accept');
    }

    /**
     * Widget JavaScript embebible
     * GET /public/radio/widget.js
     * 
     * @return \Illuminate\Http\Response
     */
    public function widget()
    {
        $js = $this->getWidgetJS();
        
        return response($js)
            ->header('Content-Type', 'application/javascript')
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Cache-Control', 'public, max-age=86400');
    }

    /**
     * Widget CSS
     * GET /public/radio/widget.css
     * 
     * @return \Illuminate\Http\Response
     */
    public function widgetCSS()
    {
        $css = $this->getWidgetCSS();
        
        return response($css)
            ->header('Content-Type', 'text/css')
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Cache-Control', 'public, max-age=86400');
    }

    /**
     * Obtener todas las estaciones con formato estandarizado
     * TOTAL: 35 estaciones de radio colombianas
     */
    private function getAllStations()
    {
        return [
            // =====================
            // MEDELLÍN - ANTIOQUIA (8 estaciones)
            // =====================
            [
                'id' => 'co-medellin-mega',
                'name' => 'La Mega 92.9 FM Medellín',
                'stream_url' => 'https://us-b4-p-e-qg12-audio.cdn.mdstrm.com/live-audio-aw/632cb48f613bac0856b931ab',
                'logo' => 'https://cdn-profiles.tunein.com/s34144/images/logog.png',
                'tags' => 'pop,rock,hits',
                'country' => 'Colombia',
                'state' => 'Antioquia',
                'city' => 'Medellín',
                'frequency' => '92.9 FM',
                'bitrate' => 128,
                'votes' => 1200
            ],
            [
                'id' => 'co-medellin-olimpica',
                'name' => 'Olímpica Stereo 104.9 FM Medellín',
                'stream_url' => 'https://26683.live.streamtheworld.com:443/OLP_MEDELLINAAC.aac',
                'logo' => 'https://olimpicastereo.com.co/wp-content/uploads/2019/12/Logo_olimpica_stereologo.png',
                'tags' => 'tropical,vallenato,salsa',
                'country' => 'Colombia',
                'state' => 'Antioquia',
                'city' => 'Medellín',
                'frequency' => '104.9 FM',
                'bitrate' => 128,
                'votes' => 1150
            ],
            [
                'id' => 'co-medellin-elsol',
                'name' => 'El Sol 107.9 FM Medellín',
                'stream_url' => 'https://us-b4-p-e-qg12-audio.cdn.mdstrm.com/live-audio-aw/632c9d30aa9ace684913b853',
                'logo' => 'https://static.wordpress.rcnradio.com/elsol/favicon/apple-touch-icon.png',
                'tags' => 'tropical,salsa,crossover',
                'country' => 'Colombia',
                'state' => 'Antioquia',
                'city' => 'Medellín',
                'frequency' => '107.9 FM',
                'bitrate' => 128,
                'votes' => 1100
            ],
            [
                'id' => 'co-medellin-mix',
                'name' => 'Mix 89.9 FM Medellín',
                'stream_url' => 'https://playerservices.streamtheworld.com/api/livestream-redirect/MIX_MEDELLINAAC.aac',
                'logo' => 'https://upload.wikimedia.org/wikipedia/commons/4/42/Mix_Colombia_logo.png',
                'tags' => 'pop,hits,urban',
                'country' => 'Colombia',
                'state' => 'Antioquia',
                'city' => 'Medellín',
                'frequency' => '89.9 FM',
                'bitrate' => 128,
                'votes' => 1080
            ],
            [
                'id' => 'co-medellin-tropicana',
                'name' => 'Tropicana 98.9 FM Medellín',
                'stream_url' => 'https://26563.live.streamtheworld.com:443/TR_MEDELLINAAC.aac',
                'logo' => 'https://www.tropicanafm.com/wp-content/uploads/2021/02/cropped-favicon_tropicana-180x180.png',
                'tags' => 'tropical,salsa',
                'country' => 'Colombia',
                'state' => 'Antioquia',
                'city' => 'Medellín',
                'frequency' => '98.9 FM',
                'bitrate' => 128,
                'votes' => 1050
            ],
            [
                'id' => 'co-medellin-besame',
                'name' => 'Bésame 94.9 FM Medellín',
                'stream_url' => 'https://26683.live.streamtheworld.com:443/BESAME_MEDELLINAAC.aac',
                'logo' => 'https://www.besame.fm/wp-content/uploads/2020/11/cropped-favicon-besame-32x32.png',
                'tags' => 'romantica,baladas',
                'country' => 'Colombia',
                'state' => 'Antioquia',
                'city' => 'Medellín',
                'frequency' => '94.9 FM',
                'bitrate' => 128,
                'votes' => 1000
            ],
            [
                'id' => 'co-medellin-radioacktiva',
                'name' => 'Radioacktiva 102.3 FM Medellín',
                'stream_url' => 'https://26663.live.streamtheworld.com:443/RADIOACKTIVA_MEDAAC.aac',
                'logo' => 'https://www.radioacktiva.com/wp-content/uploads/2024/12/cropped-favicon-new1-180x180.png',
                'tags' => 'rock,metal,alternativo',
                'country' => 'Colombia',
                'state' => 'Antioquia',
                'city' => 'Medellín',
                'frequency' => '102.3 FM',
                'bitrate' => 128,
                'votes' => 980
            ],
            [
                'id' => 'co-medellin-estrella',
                'name' => 'Estrella Estéreo 104.3 FM Medellín',
                'stream_url' => 'http://68.233.236.92:8006/;',
                'logo' => 'https://estrellacolombia.com/wp-content/uploads/2017/01/cropped-estrella_estereo-01-180x180.png',
                'tags' => 'tropical,popular',
                'country' => 'Colombia',
                'state' => 'Antioquia',
                'city' => 'Medellín',
                'frequency' => '104.3 FM',
                'bitrate' => 128,
                'votes' => 800
            ],

            // =====================
            // BOGOTÁ (14 estaciones)
            // =====================
            [
                'id' => 'co-bogota-blu',
                'name' => 'Blu Radio 89.9 FM Bogotá',
                'stream_url' => 'http://24413.live.streamtheworld.com:3690/BLURADIO_SC',
                'logo' => 'https://cdn-profiles.tunein.com/s224698/images/logog.png',
                'tags' => 'noticias,talk',
                'country' => 'Colombia',
                'state' => 'Bogota',
                'city' => 'Bogotá',
                'frequency' => '89.9 FM',
                'bitrate' => 128,
                'votes' => 1300
            ],
            [
                'id' => 'co-bogota-caracol',
                'name' => 'Caracol Radio 100.9 FM Bogotá',
                'stream_url' => 'https://26683.live.streamtheworld.com:443/CARACOL_RADIOAAC.aac',
                'logo' => 'https://caracol.com.co/pf/resources/caracol-colombia/touch-icon-ipad.png?d=174',
                'tags' => 'noticias,deportes',
                'country' => 'Colombia',
                'state' => 'Bogota',
                'city' => 'Bogotá',
                'frequency' => '100.9 FM',
                'bitrate' => 128,
                'votes' => 1280
            ],
            [
                'id' => 'co-bogota-wradio',
                'name' => 'W Radio 99.9 FM Bogotá',
                'stream_url' => 'https://26643.live.streamtheworld.com:443/WRADIOAAC.aac',
                'logo' => 'https://www.wradio.com.co/pf/resources/wradio-colombia/touch-icon-ipad.png?d=248',
                'tags' => 'noticias,talk',
                'country' => 'Colombia',
                'state' => 'Bogota',
                'city' => 'Bogotá',
                'frequency' => '99.9 FM',
                'bitrate' => 128,
                'votes' => 1250
            ],
            [
                'id' => 'co-bogota-olimpica',
                'name' => 'Olímpica Stereo 105.9 FM Bogotá',
                'stream_url' => 'https://27403.live.streamtheworld.com:443/OLP_BOGOTAAAC.aac',
                'logo' => 'https://olimpicastereo.com.co/wp-content/uploads/2019/12/Logo_olimpica_stereologo.png',
                'tags' => 'tropical,vallenato',
                'country' => 'Colombia',
                'state' => 'Bogota',
                'city' => 'Bogotá',
                'frequency' => '105.9 FM',
                'bitrate' => 128,
                'votes' => 1200
            ],
            [
                'id' => 'co-bogota-los40',
                'name' => 'LOS40 Colombia',
                'stream_url' => 'https://playerservices.streamtheworld.com/api/livestream-redirect/LOS40_COLOMBIAAAC.aac',
                'logo' => 'https://cdn-profiles.tunein.com/p1306541/images/logod.png',
                'tags' => 'pop,hits',
                'country' => 'Colombia',
                'state' => 'Bogota',
                'city' => 'Bogotá',
                'frequency' => '102.3 FM',
                'bitrate' => 128,
                'votes' => 1180
            ],
            [
                'id' => 'co-bogota-tropicana',
                'name' => 'Tropicana 102.9 FM Bogotá',
                'stream_url' => 'http://14073.live.streamtheworld.com:3690/TROPICANA_SC',
                'logo' => 'https://www.tropicanafm.com/wp-content/uploads/2021/02/cropped-favicon_tropicana-180x180.png',
                'tags' => 'tropical,salsa',
                'country' => 'Colombia',
                'state' => 'Bogota',
                'city' => 'Bogotá',
                'frequency' => '102.9 FM',
                'bitrate' => 128,
                'votes' => 1150
            ],
            [
                'id' => 'co-bogota-radioacktiva',
                'name' => 'Radioacktiva 97.9 FM Bogotá',
                'stream_url' => 'https://playerservices.streamtheworld.com/api/livestream-redirect/RADIO_ACTIVAAAC.aac',
                'logo' => 'https://www.radioacktiva.com/wp-content/uploads/2024/12/cropped-favicon-new1-180x180.png',
                'tags' => 'rock,metal',
                'country' => 'Colombia',
                'state' => 'Bogota',
                'city' => 'Bogotá',
                'frequency' => '97.9 FM',
                'bitrate' => 128,
                'votes' => 1100
            ],
            [
                'id' => 'co-bogota-candela',
                'name' => 'Candela Estéreo 101.9 FM Bogotá',
                'stream_url' => 'http://24403.live.streamtheworld.com/CANDELAESTEREO_SC',
                'logo' => 'https://uploads.candelaestereo.com/1/2019/06/cropped-logo-candela-llama-100x100.png',
                'tags' => 'tropical,popular',
                'country' => 'Colombia',
                'state' => 'Bogota',
                'city' => 'Bogotá',
                'frequency' => '101.9 FM',
                'bitrate' => 128,
                'votes' => 1050
            ],
            [
                'id' => 'co-bogota-vibra',
                'name' => 'Vibra Bogotá 104.9 FM',
                'stream_url' => 'http://27363.live.streamtheworld.com/VIBRAAAC_SC',
                'logo' => 'https://cdn-profiles.tunein.com/s33998/images/logog.png',
                'tags' => 'pop,hits,urban',
                'country' => 'Colombia',
                'state' => 'Bogota',
                'city' => 'Bogotá',
                'frequency' => '104.9 FM',
                'bitrate' => 128,
                'votes' => 1000
            ],
            [
                'id' => 'co-bogota-radionica',
                'name' => 'Radiónica 99.1 FM Bogotá',
                'stream_url' => 'http://shoutcast.rtvc.gov.co:8010/;',
                'logo' => 'https://cdn-profiles.tunein.com/s9541/images/logog.png',
                'tags' => 'alternativo,indie,rock',
                'country' => 'Colombia',
                'state' => 'Bogota',
                'city' => 'Bogotá',
                'frequency' => '99.1 FM',
                'bitrate' => 128,
                'votes' => 950
            ],

            [
                'id' => 'co-bogota-lax',
                'name' => 'La X Bogotá',
                'stream_url' => 'https://www.laxmasmusica.com/static/web_dataNormal.mp3',
                'logo' => 'http://www.laxmasmusica.com/static/images/apple-icon-180x180.aab671daf60d.png',
                'tags' => 'rock,alternativo',
                'country' => 'Colombia',
                'state' => 'Bogota',
                'city' => 'Bogotá',
                'frequency' => '103.9 FM',
                'bitrate' => 128,
                'votes' => 900
            ],
            [
                'id' => 'co-bogota-maria',
                'name' => 'Radio María Colombia',
                'stream_url' => 'http://dreamsiteradiocp.com:8076/',
                'logo' => 'https://cdn-profiles.tunein.com/s68925/images/logog.png',
                'tags' => 'cristiana,religiosa',
                'country' => 'Colombia',
                'state' => 'Bogota',
                'city' => 'Bogotá',
                'frequency' => '1220 AM',
                'bitrate' => 128,
                'votes' => 850
            ],
            [
                'id' => 'co-bogota-superclasica',
                'name' => 'Super Clásica Bogotá',
                'stream_url' => 'http://77.73.69.238:8000/stream',
                'logo' => 'http://cdn-radiotime-logos.tunein.com/s161501q.png',
                'tags' => 'clasicos,baladas',
                'country' => 'Colombia',
                'state' => 'Bogota',
                'city' => 'Bogotá',
                'frequency' => 'Online',
                'bitrate' => 128,
                'votes' => 850
            ],

            // =====================
            // CALI - VALLE (5 estaciones)
            // =====================
            [
                'id' => 'co-cali-olimpica',
                'name' => 'Olímpica Stereo 104.5 FM Cali',
                'stream_url' => 'https://playerservices.streamtheworld.com/api/livestream-redirect/OLP_CALIAAC.aac',
                'logo' => 'https://olimpicastereo.com.co/wp-content/uploads/2019/12/Logo_olimpica_stereologo.png',
                'tags' => 'salsa,tropical',
                'country' => 'Colombia',
                'state' => 'Valle del Cauca',
                'city' => 'Cali',
                'frequency' => '104.5 FM',
                'bitrate' => 128,
                'votes' => 1000
            ],
            [
                'id' => 'co-cali-tropicana',
                'name' => 'Tropicana 93.1 FM Cali',
                'stream_url' => 'http://26503.live.streamtheworld.com:80/TR_CALI.mp3',
                'logo' => 'https://www.tropicanafm.com/wp-content/uploads/2021/02/cropped-favicon_tropicana-180x180.png',
                'tags' => 'salsa,tropical',
                'country' => 'Colombia',
                'state' => 'Valle del Cauca',
                'city' => 'Cali',
                'frequency' => '93.1 FM',
                'bitrate' => 128,
                'votes' => 950
            ],
            [
                'id' => 'co-cali-mix',
                'name' => 'MIX 102.5 FM Cali',
                'stream_url' => 'https://playerservices.streamtheworld.com/api/livestream-redirect/MIX_CALIAAC.aac',
                'logo' => 'https://upload.wikimedia.org/wikipedia/commons/4/42/Mix_Colombia_logo.png',
                'tags' => 'pop,hits,urban',
                'country' => 'Colombia',
                'state' => 'Valle del Cauca',
                'city' => 'Cali',
                'frequency' => '102.5 FM',
                'bitrate' => 128,
                'votes' => 920
            ],
            [
                'id' => 'co-cali-lax',
                'name' => 'LA X 96.5 FM Cali',
                'stream_url' => 'https://tupanel.info:2000/stream/2digitalradioHDsslLIVE040',
                'logo' => 'https://cdn-radiotime-logos.tunein.com/s86611d.png',
                'tags' => 'rock,alternativo',
                'country' => 'Colombia',
                'state' => 'Valle del Cauca',
                'city' => 'Cali',
                'frequency' => '96.5 FM',
                'bitrate' => 128,
                'votes' => 880
            ],
            [
                'id' => 'co-cali-energia',
                'name' => 'Energía 102.5 FM Cali',
                'stream_url' => 'https://audio1.energia1025.com/radio/8000/stream.mp3',
                'logo' => 'https://energia1025.com/wp-content/uploads/2019/11/cropped-logoenergia-2-180x180.png',
                'tags' => 'reggaeton,urbano',
                'country' => 'Colombia',
                'state' => 'Valle del Cauca',
                'city' => 'Cali',
                'frequency' => '102.5 FM',
                'bitrate' => 128,
                'votes' => 850
            ],

            // =====================
            // BARRANQUILLA - COSTA (4 estaciones)
            // =====================
            [
                'id' => 'co-barranquilla-olimpica',
                'name' => 'Olímpica Stereo 92.1 FM Barranquilla',
                'stream_url' => 'https://13693.live.streamtheworld.com:443/OLP_BARRANQUILLAAAC.aac',
                'logo' => 'https://olimpicastereo.com.co/wp-content/uploads/2019/12/Logo_olimpica_stereologo.png',
                'tags' => 'vallenato,tropical',
                'country' => 'Colombia',
                'state' => 'Atlantico',
                'city' => 'Barranquilla',
                'frequency' => '92.1 FM',
                'bitrate' => 128,
                'votes' => 1050
            ],
            [
                'id' => 'co-barranquilla-tiempo',
                'name' => 'Radio Tiempo Barranquilla',
                'stream_url' => 'https://master.letio.com/io/?gs=9100&type=master.m3u8',
                'logo' => 'https://www.radiotiempo.co/icon?b8748d2f62517688',
                'tags' => 'vallenato,tropical',
                'country' => 'Colombia',
                'state' => 'Atlantico',
                'city' => 'Barranquilla',
                'frequency' => '96.1 FM',
                'bitrate' => 128,
                'votes' => 950
            ],
            [
                'id' => 'co-barranquilla-mix',
                'name' => 'Mix Radio 103.9 FM Barranquilla',
                'stream_url' => 'https://master.letio.com/io/?gs=9156&type=master.m3u8',
                'logo' => 'https://upload.wikimedia.org/wikipedia/commons/4/42/Mix_Colombia_logo.png',
                'tags' => 'pop,hits',
                'country' => 'Colombia',
                'state' => 'Atlantico',
                'city' => 'Barranquilla',
                'frequency' => '103.9 FM',
                'bitrate' => 128,
                'votes' => 900
            ],
            [
                'id' => 'co-barranquilla-vallenatisima',
                'name' => 'Mi Vallenatísima',
                'stream_url' => 'https://streaming.radiosenlinea.com.ar:10865/stream',
                'logo' => 'https://cdn-profiles.tunein.com/s293652/images/logog.png',
                'tags' => 'vallenato',
                'country' => 'Colombia',
                'state' => 'Atlantico',
                'city' => 'Barranquilla',
                'frequency' => 'Online',
                'bitrate' => 128,
                'votes' => 850
            ],

            // =====================
            // CARTAGENA (1 estación)
            // =====================
            [
                'id' => 'co-cartagena-tiempo',
                'name' => 'Radio Tiempo Cartagena',
                'stream_url' => 'https://20603.live.streamtheworld.com:443/RT_CARTAGENAAAC.aac',
                'logo' => 'https://radiotiempo.co/icon?b8748d2f62517688',
                'tags' => 'tropical,salsa',
                'country' => 'Colombia',
                'state' => 'Bolivar',
                'city' => 'Cartagena',
                'frequency' => '97.3 FM',
                'bitrate' => 128,
                'votes' => 900
            ],

            // =====================
            // GÉNEROS ESPECIALES (3 estaciones)
            // =====================
            [
                'id' => 'co-vallenato-123',
                'name' => '123 Vallenato',
                'stream_url' => 'https://radiolatina.info:10903/;',
                'logo' => 'https://cdn-profiles.tunein.com/s296696/images/logog.png',
                'tags' => 'vallenato',
                'country' => 'Colombia',
                'state' => 'Nacional',
                'city' => 'Colombia',
                'frequency' => 'Online',
                'bitrate' => 128,
                'votes' => 950
            ],
            [
                'id' => 'co-salsa-dura',
                'name' => 'Colombia Salsa Dura',
                'stream_url' => 'https://play10.tikast.com/proxy/colsalsadura?mp=/stream',
                'logo' => 'https://colombiacrossover.com/wp-content/uploads/Logo-colombia-salsa-dura-png-1024x985.png',
                'tags' => 'salsa',
                'country' => 'Colombia',
                'state' => 'Nacional',
                'city' => 'Colombia',
                'frequency' => 'Online',
                'bitrate' => 128,
                'votes' => 880
            ],
            [
                'id' => 'co-llanera',
                'name' => 'Música Llanera',
                'stream_url' => 'http://192.99.203.81:8704/',
                'logo' => 'https://www.musicallanera.co/wp-content/uploads/2015/10/cropped-emoticon-vaquero-llanero-1-180x180.png',
                'tags' => 'llanera,joropo',
                'country' => 'Colombia',
                'state' => 'Casanare',
                'city' => 'Yopal',
                'frequency' => 'Online',
                'bitrate' => 128,
                'votes' => 800
            ],
        ];
    }

    /**
     * Generar JavaScript del Widget
     */
    private function getWidgetJS()
    {
        return <<<'JS'
/**
 * 📻 Radio 105 FM - Widget Embebible
 * @version 1.0.0
 * @author 105POS
 * @license MIT
 * 
 * Uso:
 * <script src="https://105pos.pro/public/radio/widget.js"></script>
 * <div id="radio-105"></div>
 * <script>Radio105.init({ container: '#radio-105' });</script>
 */
(function(window, document) {
    'use strict';

    const API_BASE = 'https://105pos.pro/api/public/radio';
    
    const Radio105 = {
        version: '1.0.0',
        audio: null,
        currentStation: null,
        isPlaying: false,
        stations: [],
        favorites: [],
        container: null,
        options: {
            container: '#radio-105',
            theme: 'dark', // 'dark' | 'light' | 'auto'
            primaryColor: '#10b981', // emerald-500
            showFavorites: true,
            autoplay: false,
            defaultCity: null,
            compact: false,
            height: '500px'
        },

        // Inicializar el widget
        init: async function(options = {}) {
            this.options = { ...this.options, ...options };
            this.container = document.querySelector(this.options.container);
            
            if (!this.container) {
                console.error('Radio 105: Container not found:', this.options.container);
                return;
            }

            // Cargar CSS
            this.loadStyles();
            
            // Crear audio element
            this.audio = new Audio();
            this.audio.crossOrigin = 'anonymous';
            
            // Cargar favoritos del localStorage
            this.loadFavorites();
            
            // Cargar estaciones
            await this.loadStations();
            
            // Renderizar UI
            this.render();
            
            // Eventos del audio
            this.setupAudioEvents();

            console.log('📻 Radio 105 FM Widget initialized');
        },

        // Cargar estilos CSS
        loadStyles: function() {
            if (document.getElementById('radio-105-styles')) return;
            
            const link = document.createElement('link');
            link.id = 'radio-105-styles';
            link.rel = 'stylesheet';
            link.href = API_BASE + '/styles';
            document.head.appendChild(link);
        },

        // Cargar estaciones desde la API
        loadStations: async function() {
            try {
                const params = new URLSearchParams();
                if (this.options.defaultCity) {
                    params.append('state', this.options.defaultCity);
                }
                
                const response = await fetch(`${API_BASE}/stations?${params}`);
                const data = await response.json();
                
                if (data.success) {
                    this.stations = data.data;
                }
            } catch (error) {
                console.error('Radio 105: Error loading stations:', error);
            }
        },

        // Cargar favoritos
        loadFavorites: function() {
            try {
                const saved = localStorage.getItem('radio105_favorites');
                this.favorites = saved ? JSON.parse(saved) : [];
            } catch (e) {
                this.favorites = [];
            }
        },

        // Guardar favoritos
        saveFavorites: function() {
            localStorage.setItem('radio105_favorites', JSON.stringify(this.favorites));
        },

        // Toggle favorito
        toggleFavorite: function(stationId) {
            const index = this.favorites.indexOf(stationId);
            if (index > -1) {
                this.favorites.splice(index, 1);
            } else {
                this.favorites.push(stationId);
            }
            this.saveFavorites();
            this.render();
        },

        // Reproducir estación
        play: function(station) {
            if (typeof station === 'string') {
                station = this.stations.find(s => s.id === station);
            }
            
            if (!station) return;

            this.currentStation = station;
            this.audio.src = station.stream_url;
            this.audio.play().catch(e => console.error('Radio 105: Play error:', e));
            this.isPlaying = true;
            this.render();
        },

        // Pausar
        pause: function() {
            this.audio.pause();
            this.isPlaying = false;
            this.render();
        },

        // Toggle play/pause
        toggle: function() {
            if (this.isPlaying) {
                this.pause();
            } else if (this.currentStation) {
                this.audio.play();
                this.isPlaying = true;
                this.render();
            }
        },

        // Configurar eventos del audio
        setupAudioEvents: function() {
            this.audio.addEventListener('playing', () => {
                this.isPlaying = true;
                this.render();
            });
            
            this.audio.addEventListener('pause', () => {
                this.isPlaying = false;
                this.render();
            });
            
            this.audio.addEventListener('error', (e) => {
                console.error('Radio 105: Audio error:', e);
                this.isPlaying = false;
                this.render();
            });
        },

        // Renderizar UI
        render: function() {
            const theme = this.options.theme === 'auto' 
                ? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')
                : this.options.theme;

            const primaryColor = this.options.primaryColor;
            
            this.container.innerHTML = `
                <div class="radio105-widget radio105-${theme}" style="--radio105-primary: ${primaryColor}; height: ${this.options.height};">
                    <!-- Header -->
                    <div class="radio105-header">
                        <div class="radio105-header-content">
                            <h2 class="radio105-title">📻 Radio 105 FM</h2>
                            <span class="radio105-subtitle">Streaming Colombia</span>
                        </div>
                        ${this.isPlaying ? '<div class="radio105-equalizer"><span></span><span></span><span></span></div>' : ''}
                    </div>
                    
                    <!-- Now Playing -->
                    ${this.currentStation ? `
                    <div class="radio105-now-playing">
                        <img src="${this.currentStation.logo}" alt="${this.currentStation.name}" class="radio105-now-logo" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect fill=%22%23374151%22 width=%22100%22 height=%22100%22/><text x=%2250%22 y=%2255%22 font-size=%2240%22 text-anchor=%22middle%22>📻</text></svg>'">
                        <div class="radio105-now-info">
                            <div class="radio105-now-name">${this.currentStation.name}</div>
                            <div class="radio105-now-meta">${this.currentStation.city} • ${this.currentStation.frequency}</div>
                        </div>
                        <button class="radio105-play-btn" onclick="Radio105.toggle()">
                            ${this.isPlaying ? 
                                '<svg viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>' : 
                                '<svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>'
                            }
                        </button>
                    </div>
                    ` : ''}
                    
                    <!-- Station List -->
                    <div class="radio105-stations">
                        ${this.stations.map(station => `
                            <div class="radio105-station ${this.currentStation?.id === station.id ? 'radio105-station-active' : ''}" onclick="Radio105.play('${station.id}')">
                                <img src="${station.logo}" alt="${station.name}" class="radio105-station-logo" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect fill=%22%23374151%22 width=%22100%22 height=%22100%22/><text x=%2250%22 y=%2255%22 font-size=%2240%22 text-anchor=%22middle%22>📻</text></svg>'">
                                <div class="radio105-station-info">
                                    <div class="radio105-station-name">${station.name}</div>
                                    <div class="radio105-station-meta">${station.city} • ${station.tags.split(',')[0]}</div>
                                </div>
                                <button class="radio105-fav-btn ${this.favorites.includes(station.id) ? 'radio105-fav-active' : ''}" onclick="event.stopPropagation(); Radio105.toggleFavorite('${station.id}')">
                                    <svg viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                </button>
                            </div>
                        `).join('')}
                    </div>
                    
                    <!-- Footer -->
                    <div class="radio105-footer">
                        <a href="https://105pos.pro" target="_blank" class="radio105-powered">
                            Powered by 105POS
                        </a>
                    </div>
                </div>
            `;
        },

        // Destruir widget
        destroy: function() {
            if (this.audio) {
                this.audio.pause();
                this.audio = null;
            }
            if (this.container) {
                this.container.innerHTML = '';
            }
        }
    };

    // Exponer globalmente
    window.Radio105 = Radio105;

})(window, document);
JS;
    }

    /**
     * Generar CSS del Widget
     */
    private function getWidgetCSS()
    {
        return <<<'CSS'
/* Radio 105 FM Widget Styles */
.radio105-widget {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    border-radius: 16px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.radio105-dark {
    background: #18181b;
    color: #fff;
}

.radio105-light {
    background: #fff;
    color: #18181b;
    border: 1px solid #e5e7eb;
}

/* Header */
.radio105-header {
    background: linear-gradient(135deg, var(--radio105-primary, #10b981), #059669);
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.radio105-title {
    font-size: 20px;
    font-weight: 700;
    margin: 0;
}

.radio105-subtitle {
    font-size: 12px;
    opacity: 0.8;
}

/* Equalizer Animation */
.radio105-equalizer {
    display: flex;
    align-items: flex-end;
    gap: 3px;
    height: 20px;
}

.radio105-equalizer span {
    width: 4px;
    background: rgba(255,255,255,0.8);
    border-radius: 2px;
    animation: radio105-eq 0.5s ease-in-out infinite alternate;
}

.radio105-equalizer span:nth-child(1) { height: 8px; animation-delay: 0s; }
.radio105-equalizer span:nth-child(2) { height: 16px; animation-delay: 0.2s; }
.radio105-equalizer span:nth-child(3) { height: 12px; animation-delay: 0.4s; }

@keyframes radio105-eq {
    0% { transform: scaleY(0.5); }
    100% { transform: scaleY(1); }
}

/* Now Playing */
.radio105-now-playing {
    display: flex;
    align-items: center;
    padding: 16px;
    gap: 12px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.radio105-light .radio105-now-playing {
    border-color: #e5e7eb;
}

.radio105-now-logo {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    object-fit: cover;
}

.radio105-now-info {
    flex: 1;
    min-width: 0;
}

.radio105-now-name {
    font-weight: 600;
    font-size: 16px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.radio105-now-meta {
    font-size: 12px;
    opacity: 0.6;
    margin-top: 4px;
}

.radio105-play-btn {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: var(--radio105-primary, #10b981);
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s, background 0.2s;
}

.radio105-play-btn:hover {
    transform: scale(1.1);
}

.radio105-play-btn svg {
    width: 24px;
    height: 24px;
    fill: white;
}

/* Station List */
.radio105-stations {
    flex: 1;
    overflow-y: auto;
    padding: 8px;
}

.radio105-station {
    display: flex;
    align-items: center;
    padding: 12px;
    gap: 12px;
    border-radius: 12px;
    cursor: pointer;
    transition: background 0.2s;
}

.radio105-dark .radio105-station:hover {
    background: rgba(255,255,255,0.05);
}

.radio105-light .radio105-station:hover {
    background: #f3f4f6;
}

.radio105-station-active {
    background: rgba(16, 185, 129, 0.15) !important;
}

.radio105-station-logo {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    object-fit: cover;
}

.radio105-station-info {
    flex: 1;
    min-width: 0;
}

.radio105-station-name {
    font-weight: 600;
    font-size: 14px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.radio105-station-meta {
    font-size: 11px;
    opacity: 0.5;
    margin-top: 2px;
}

.radio105-fav-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: none;
    background: transparent;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
}

.radio105-fav-btn:hover {
    background: rgba(255,255,255,0.1);
}

.radio105-fav-btn svg {
    width: 18px;
    height: 18px;
    fill: #6b7280;
    transition: fill 0.2s;
}

.radio105-fav-active svg {
    fill: #ef4444;
}

/* Footer */
.radio105-footer {
    padding: 12px;
    text-align: center;
    border-top: 1px solid rgba(255,255,255,0.1);
}

.radio105-light .radio105-footer {
    border-color: #e5e7eb;
}

.radio105-powered {
    font-size: 11px;
    opacity: 0.5;
    text-decoration: none;
    color: inherit;
}

.radio105-powered:hover {
    opacity: 0.8;
}

/* Scrollbar */
.radio105-stations::-webkit-scrollbar {
    width: 6px;
}

.radio105-stations::-webkit-scrollbar-track {
    background: transparent;
}

.radio105-stations::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,0.2);
    border-radius: 3px;
}

.radio105-light .radio105-stations::-webkit-scrollbar-thumb {
    background: rgba(0,0,0,0.2);
}
CSS;
    }
}
