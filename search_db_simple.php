<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$results = DB::select("SELECT * FROM users WHERE name LIKE '%missing%' OR email LIKE '%missing%'");
if (count($results) > 0) {
    print_r($results);
} else {
    echo "No users found with 'missing' in name or email.\n";
}
