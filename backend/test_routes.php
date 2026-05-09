<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/storage/tenants/las_marcas/products/ie66rYICRhcMg05drSMhsTXhutQvkYFgszAWvrnz.jpg', 'GET');

$routes = app('router')->getRoutes()->get('GET');
foreach($routes as $uri => $r) {
    if (strpos($uri, 'storage/tenants') !== false) {
        echo "Testing $uri: ";
        var_dump($r->matches($request));
    }
}
