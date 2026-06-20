<?php
require __DIR__.'/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$ch = curl_init('https://generativelanguage.googleapis.com/v1beta/models?key=' . $_ENV['GEMINI_API_KEY']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($ch);
$data = json_decode($res, true);
if (isset($data['models'])) {
    foreach($data['models'] as $m) {
        if (strpos($m['name'], 'gemini') !== false) {
            echo $m['name'] . "\n";
        }
    }
} else {
    echo "No models array found.\n";
}
