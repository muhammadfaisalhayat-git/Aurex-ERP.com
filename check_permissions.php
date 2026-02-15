<?php
use Spatie\Permission\Models\Permission;
foreach (Permission::all() as $p) {
    echo "Module: " . ($p->module ?? 'N/A') . " | Name: " . $p->name . "\n";
}
