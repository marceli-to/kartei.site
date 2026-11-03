<?php

use Illuminate\Support\Facades\Schedule;

// Abandoned uploads (images uploaded but never saved to a record)
Schedule::command('media:purge-temp')->hourly()->withoutOverlapping();
