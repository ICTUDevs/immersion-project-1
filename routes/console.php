<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

app(Schedule::class)->command('generate:qr-codes')->everyFiveSeconds();
