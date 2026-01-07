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
     * URLs y logos reales de Radio Browser API
     */
    private function getDefaultColombianStations()
    {
        return [
            // =====================
            // MEDELLÍN - ANTIOQUIA
            // =====================
            [
                'stationuuid' => 'co-medellin-mega',
                'name' => 'La Mega 92.9 FM Medellín',
                'url_resolved' => 'https://us-b4-p-e-qg12-audio.cdn.mdstrm.com/live-audio-aw/632cb48f613bac0856b931ab',
                'favicon' => 'https://cdn-profiles.tunein.com/s34144/images/logog.png',
                'tags' => 'pop,rock,hits',
                'country' => 'Colombia',
                'state' => 'Antioquia',
                'votes' => 1200
            ],
            [
                'stationuuid' => 'co-medellin-olimpica',
                'name' => 'Olímpica Stereo 104.9 FM Medellín',
                'url_resolved' => 'https://26683.live.streamtheworld.com:443/OLP_MEDELLINAAC.aac',
                'favicon' => 'https://olimpicastereo.com.co/wp-content/uploads/2019/12/Logo_olimpica_stereologo.png',
                'tags' => 'tropical,vallenato,salsa',
                'country' => 'Colombia',
                'state' => 'Antioquia',
                'votes' => 1150
            ],
            [
                'stationuuid' => 'co-medellin-elsol',
                'name' => 'El Sol 107.9 FM Medellín',
                'url_resolved' => 'https://us-b4-p-e-qg12-audio.cdn.mdstrm.com/live-audio-aw/632c9d30aa9ace684913b853',
                'favicon' => 'https://static.wordpress.rcnradio.com/elsol/favicon/apple-touch-icon.png',
                'tags' => 'tropical,salsa,crossover',
                'country' => 'Colombia',
                'state' => 'Antioquia',
                'votes' => 1100
            ],
            [
                'stationuuid' => 'co-medellin-mix',
                'name' => 'Mix 89.9 FM Medellín',
                'url_resolved' => 'https://24493.live.streamtheworld.com:443/MIX_MEDELLINAAC.aac',
                'favicon' => 'https://upload.wikimedia.org/wikipedia/commons/4/42/Mix_Colombia_logo.png',
                'tags' => 'pop,hits,urban',
                'country' => 'Colombia',
                'state' => 'Antioquia',
                'votes' => 1080
            ],
            [
                'stationuuid' => 'co-medellin-tropicana',
                'name' => 'Tropicana 98.9 FM Medellín',
                'url_resolved' => 'https://26563.live.streamtheworld.com:443/TR_MEDELLINAAC.aac',
                'favicon' => 'https://www.tropicanafm.com/wp-content/uploads/2021/02/cropped-favicon_tropicana-180x180.png',
                'tags' => 'tropical,salsa',
                'country' => 'Colombia',
                'state' => 'Antioquia',
                'votes' => 1050
            ],
            [
                'stationuuid' => 'co-medellin-besame',
                'name' => 'Bésame 94.9 FM Medellín',
                'url_resolved' => 'https://26683.live.streamtheworld.com:443/BESAME_MEDELLINAAC.aac',
                'favicon' => 'https://www.besame.fm/wp-content/uploads/2020/11/cropped-favicon-besame-32x32.png',
                'tags' => 'romantica,baladas',
                'country' => 'Colombia',
                'state' => 'Antioquia',
                'votes' => 1000
            ],
            [
                'stationuuid' => 'co-medellin-radioacktiva',
                'name' => 'Radioacktiva 102.3 FM Medellín',
                'url_resolved' => 'https://26663.live.streamtheworld.com:443/RADIOACKTIVA_MEDAAC.aac',
                'favicon' => 'https://www.radioacktiva.com/wp-content/uploads/2024/12/cropped-favicon-new1-180x180.png',
                'tags' => 'rock,metal,alternativo',
                'country' => 'Colombia',
                'state' => 'Antioquia',
                'votes' => 980
            ],
            [
                'stationuuid' => 'co-medellin-estrella',
                'name' => 'Estrella Estéreo 104.3 FM',
                'url_resolved' => 'http://68.233.236.92:8006/;',
                'favicon' => 'https://estrellacolombia.com/wp-content/uploads/2017/01/cropped-estrella_estereo-01-180x180.png',
                'tags' => 'tropical,popular',
                'country' => 'Colombia',
                'state' => 'Antioquia',
                'votes' => 800
            ],

            // =====================
            // BOGOTÁ
            // =====================
            [
                'stationuuid' => 'co-bogota-blu',
                'name' => 'Blu Radio 89.9 FM Bogotá',
                'url_resolved' => 'http://24413.live.streamtheworld.com:3690/BLURADIO_SC',
                'favicon' => 'https://cdn-profiles.tunein.com/s224698/images/logog.png',
                'tags' => 'noticias,talk',
                'country' => 'Colombia',
                'state' => 'Bogota',
                'votes' => 1300
            ],
            [
                'stationuuid' => 'co-bogota-caracol',
                'name' => 'Caracol Radio 100.9 FM Bogotá',
                'url_resolved' => 'https://26683.live.streamtheworld.com:443/CARACOL_RADIOAAC.aac',
                'favicon' => 'https://caracol.com.co/pf/resources/caracol-colombia/touch-icon-ipad.png?d=174',
                'tags' => 'noticias,deportes',
                'country' => 'Colombia',
                'state' => 'Bogota',
                'votes' => 1280
            ],
            [
                'stationuuid' => 'co-bogota-wradio',
                'name' => 'W Radio 99.9 FM Bogotá',
                'url_resolved' => 'https://26643.live.streamtheworld.com:443/WRADIOAAC.aac',
                'favicon' => 'https://www.wradio.com.co/pf/resources/wradio-colombia/touch-icon-ipad.png?d=248',
                'tags' => 'noticias,talk',
                'country' => 'Colombia',
                'state' => 'Bogota',
                'votes' => 1250
            ],
            [
                'stationuuid' => 'co-bogota-olimpica',
                'name' => 'Olímpica Stereo 105.9 FM Bogotá',
                'url_resolved' => 'https://27403.live.streamtheworld.com:443/OLP_BOGOTAAAC.aac',
                'favicon' => 'https://olimpicastereo.com.co/wp-content/uploads/2019/12/Logo_olimpica_stereologo.png',
                'tags' => 'tropical,vallenato',
                'country' => 'Colombia',
                'state' => 'Bogota',
                'votes' => 1200
            ],
            [
                'stationuuid' => 'co-bogota-los40',
                'name' => 'LOS40 Colombia',
                'url_resolved' => 'https://24413.live.streamtheworld.com:443/LOS40_COLOMBIAAAC.aac',
                'favicon' => 'https://cdn-profiles.tunein.com/p1306541/images/logod.png',
                'tags' => 'pop,hits',
                'country' => 'Colombia',
                'state' => 'Bogota',
                'votes' => 1180
            ],
            [
                'stationuuid' => 'co-bogota-tropicana',
                'name' => 'Tropicana 102.9 FM Bogotá',
                'url_resolved' => 'http://14073.live.streamtheworld.com:3690/TROPICANA_SC',
                'favicon' => 'https://www.tropicanafm.com/wp-content/uploads/2021/02/cropped-favicon_tropicana-180x180.png',
                'tags' => 'tropical,salsa',
                'country' => 'Colombia',
                'state' => 'Bogota',
                'votes' => 1150
            ],
            [
                'stationuuid' => 'co-bogota-radioacktiva',
                'name' => 'Radioacktiva 97.9 FM Bogotá',
                'url_resolved' => 'https://27693.live.streamtheworld.com:443/RADIO_ACTIVAAAC.aac',
                'favicon' => 'https://www.radioacktiva.com/wp-content/uploads/2024/12/cropped-favicon-new1-180x180.png',
                'tags' => 'rock,metal',
                'country' => 'Colombia',
                'state' => 'Bogota',
                'votes' => 1100
            ],
            [
                'stationuuid' => 'co-bogota-candela',
                'name' => 'Candela Estéreo 101.9 FM Bogotá',
                'url_resolved' => 'http://24403.live.streamtheworld.com/CANDELAESTEREO_SC',
                'favicon' => 'https://uploads.candelaestereo.com/1/2019/06/cropped-logo-candela-llama-100x100.png',
                'tags' => 'tropical,popular',
                'country' => 'Colombia',
                'state' => 'Bogota',
                'votes' => 1050
            ],
            [
                'stationuuid' => 'co-bogota-vibra',
                'name' => 'Vibra Bogotá 104.9 FM',
                'url_resolved' => 'http://27363.live.streamtheworld.com/VIBRAAAC_SC',
                'favicon' => 'https://cdn-profiles.tunein.com/s33998/images/logog.png',
                'tags' => 'pop,hits,urban',
                'country' => 'Colombia',
                'state' => 'Bogota',
                'votes' => 1000
            ],
            [
                'stationuuid' => 'co-bogota-radionica',
                'name' => 'Radiónica 99.1 FM Bogotá',
                'url_resolved' => 'http://shoutcast.rtvc.gov.co:8010/;',
                'favicon' => 'https://cdn-profiles.tunein.com/s9541/images/logog.png',
                'tags' => 'alternativo,indie,rock',
                'country' => 'Colombia',
                'state' => 'Bogota',
                'votes' => 950
            ],
            [
                'stationuuid' => 'co-bogota-lakalle',
                'name' => 'La Kalle Bogotá',
                'url_resolved' => 'http://27423.live.streamtheworld.com/LA_KALLE_SC',
                'favicon' => 'https://cdn-profiles.tunein.com/s34136/images/logog.png',
                'tags' => 'reggaeton,urbano',
                'country' => 'Colombia',
                'state' => 'Bogota',
                'votes' => 920
            ],
            [
                'stationuuid' => 'co-bogota-lax',
                'name' => 'La X Bogotá',
                'url_resolved' => 'https://www.laxmasmusica.com/static/web_dataNormal.mp3',
                'favicon' => 'http://www.laxmasmusica.com/static/images/apple-icon-180x180.aab671daf60d.png',
                'tags' => 'rock,alternativo',
                'country' => 'Colombia',
                'state' => 'Bogota',
                'votes' => 900
            ],
            [
                'stationuuid' => 'co-bogota-maria',
                'name' => 'Radio María Colombia',
                'url_resolved' => 'http://dreamsiteradiocp.com:8076/',
                'favicon' => 'https://cdn-profiles.tunein.com/s68925/images/logog.png',
                'tags' => 'cristiana,religiosa',
                'country' => 'Colombia',
                'state' => 'Bogota',
                'votes' => 850
            ],

            // =====================
            // CALI - VALLE DEL CAUCA
            // =====================
            [
                'stationuuid' => 'co-cali-olimpica',
                'name' => 'Olímpica Stereo 104.5 FM Cali',
                'url_resolved' => 'https://27593.live.streamtheworld.com:443/OLP_CALI.mp3',
                'favicon' => 'https://olimpicastereo.com.co/wp-content/uploads/2019/12/Logo_olimpica_stereologo.png',
                'tags' => 'salsa,tropical',
                'country' => 'Colombia',
                'state' => 'Valle del Cauca',
                'votes' => 1000
            ],
            [
                'stationuuid' => 'co-cali-tropicana',
                'name' => 'Tropicana 93.1 FM Cali',
                'url_resolved' => 'http://26503.live.streamtheworld.com:80/TR_CALI.mp3',
                'favicon' => 'https://www.tropicanafm.com/wp-content/uploads/2021/02/cropped-favicon_tropicana-180x180.png',
                'tags' => 'salsa,tropical',
                'country' => 'Colombia',
                'state' => 'Valle del Cauca',
                'votes' => 950
            ],
            [
                'stationuuid' => 'co-cali-mix',
                'name' => 'MIX 102.5 FM Cali',
                'url_resolved' => 'https://14133.live.streamtheworld.com/MIX_CALIAAC_SC',
                'favicon' => 'https://upload.wikimedia.org/wikipedia/commons/4/42/Mix_Colombia_logo.png',
                'tags' => 'pop,hits,urban',
                'country' => 'Colombia',
                'state' => 'Valle del Cauca',
                'votes' => 920
            ],
            [
                'stationuuid' => 'co-cali-lax',
                'name' => 'LA X 96.5 FM Cali',
                'url_resolved' => 'https://tupanel.info:2000/stream/2digitalradioHDsslLIVE040',
                'favicon' => 'https://cdn-radiotime-logos.tunein.com/s86611d.png',
                'tags' => 'rock,alternativo',
                'country' => 'Colombia',
                'state' => 'Valle del Cauca',
                'votes' => 880
            ],
            [
                'stationuuid' => 'co-cali-energia',
                'name' => 'Energía 102.5 FM Cali',
                'url_resolved' => 'https://audio1.energia1025.com/radio/8000/stream.mp3',
                'favicon' => 'https://energia1025.com/wp-content/uploads/2019/11/cropped-logoenergia-2-180x180.png',
                'tags' => 'reggaeton,urbano',
                'country' => 'Colombia',
                'state' => 'Valle del Cauca',
                'votes' => 850
            ],

            // =====================
            // BARRANQUILLA - ATLÁNTICO (COSTA)
            // =====================
            [
                'stationuuid' => 'co-barranquilla-olimpica',
                'name' => 'Olímpica Stereo Barranquilla',
                'url_resolved' => 'https://13693.live.streamtheworld.com:443/OLP_BARRANQUILLAAAC.aac',
                'favicon' => 'https://olimpicastereo.com.co/wp-content/uploads/2019/12/Logo_olimpica_stereologo.png',
                'tags' => 'vallenato,tropical',
                'country' => 'Colombia',
                'state' => 'Atlantico',
                'votes' => 1050
            ],
            [
                'stationuuid' => 'co-barranquilla-tiempo',
                'name' => 'Radio Tiempo Barranquilla',
                'url_resolved' => 'https://master.letio.com/io/?gs=9100&type=master.m3u8',
                'favicon' => 'https://www.radiotiempo.co/icon?b8748d2f62517688',
                'tags' => 'vallenato,tropical',
                'country' => 'Colombia',
                'state' => 'Atlantico',
                'votes' => 950
            ],
            [
                'stationuuid' => 'co-barranquilla-mix',
                'name' => 'Mix Radio 103.9 FM Barranquilla',
                'url_resolved' => 'https://master.letio.com/io/?gs=9156&type=master.m3u8',
                'favicon' => 'https://upload.wikimedia.org/wikipedia/commons/4/42/Mix_Colombia_logo.png',
                'tags' => 'pop,hits',
                'country' => 'Colombia',
                'state' => 'Atlantico',
                'votes' => 900
            ],
            [
                'stationuuid' => 'co-barranquilla-vallenatisima',
                'name' => 'Mi Vallenatísima',
                'url_resolved' => 'https://streaming.radiosenlinea.com.ar:10865/stream',
                'favicon' => 'https://cdn-profiles.tunein.com/s293652/images/logog.png',
                'tags' => 'vallenato',
                'country' => 'Colombia',
                'state' => 'Atlantico',
                'votes' => 850
            ],

            // =====================
            // CARTAGENA - BOLÍVAR
            // =====================
            [
                'stationuuid' => 'co-cartagena-tiempo',
                'name' => 'Radio Tiempo Cartagena',
                'url_resolved' => 'https://20603.live.streamtheworld.com:443/RT_CARTAGENAAAC.aac',
                'favicon' => 'https://radiotiempo.co/icon?b8748d2f62517688',
                'tags' => 'tropical,salsa',
                'country' => 'Colombia',
                'state' => 'Bolivar',
                'votes' => 900
            ],

            // =====================
            // GÉNEROS ESPECIALES
            // =====================
            [
                'stationuuid' => 'co-vallenato-123',
                'name' => '123 Vallenato',
                'url_resolved' => 'https://radiolatina.info:10903/;',
                'favicon' => 'https://cdn-profiles.tunein.com/s296696/images/logog.png',
                'tags' => 'vallenato',
                'country' => 'Colombia',
                'state' => 'Medellin',
                'votes' => 950
            ],
            [
                'stationuuid' => 'co-salsa-clasica',
                'name' => 'Colombia Salsa Dura',
                'url_resolved' => 'https://play10.tikast.com/proxy/colsalsadura?mp=/stream',
                'favicon' => 'https://colombiacrossover.com/wp-content/uploads/Logo-colombia-salsa-dura-png-1024x985.png',
                'tags' => 'salsa',
                'country' => 'Colombia',
                'state' => 'Manizales',
                'votes' => 880
            ],
            [
                'stationuuid' => 'co-super-clasica',
                'name' => 'Super Clásica',
                'url_resolved' => 'http://77.73.69.238:8000/stream',
                'favicon' => 'http://cdn-radiotime-logos.tunein.com/s161501q.png',
                'tags' => 'clasicos,baladas',
                'country' => 'Colombia',
                'state' => 'Bogota',
                'votes' => 850
            ],
            [
                'stationuuid' => 'co-llanera',
                'name' => 'Música Llanera',
                'url_resolved' => 'http://192.99.203.81:8704/',
                'favicon' => 'https://www.musicallanera.co/wp-content/uploads/2015/10/cropped-emoticon-vaquero-llanero-1-180x180.png',
                'tags' => 'llanera,joropo',
                'country' => 'Colombia',
                'state' => 'Yopal',
                'votes' => 800
            ],
            [
                'stationuuid' => 'co-paisa',
                'name' => 'Paisa Estéreo',
                'url_resolved' => 'http://radiolatina.info:7094/',
                'favicon' => 'https://cdn-profiles.tunein.com/s129619/images/logog.png',
                'tags' => 'tropical,popular',
                'country' => 'Colombia',
                'state' => 'Antioquia',
                'votes' => 780
            ]
        ];
    }
}
