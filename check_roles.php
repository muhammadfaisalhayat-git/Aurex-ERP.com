<?php
use Spatie\Permission\Models\Role;
foreach (Role::all() as $role) {
    echo "ID: " . $role->id . " | Name: " . $role->name . " | Display EN: " . $role->display_name_en . " | Display AR: " . $role->display_name_ar . "\n";
}
