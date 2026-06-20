<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

foreach(\App\Models\Tenant::all() as $t) {
    $p = public_path('storage/tenants/'.$t->id);
    if(!is_link($p)) {
        @mkdir(public_path('storage/tenants'), 0755, true);
        @symlink(base_path('storage/tenant'.$t->id.'/app/public'), $p);
        echo 'Linked '.$t->id.PHP_EOL;
    }
}
echo "Done.";
