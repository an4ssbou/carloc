<?php

return [
    'allow_simulation' => env('GPS_SIMULATION_ENABLED', false),
    'poll_interval_seconds' => 5,
    'stale_after_seconds' => 60,
];
