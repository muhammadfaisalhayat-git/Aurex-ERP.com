<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('backup:run')->dailyAt('01:00');
Schedule::command('backup:clean')->dailyAt('02:00');