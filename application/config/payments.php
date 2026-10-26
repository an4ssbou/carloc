<?php

return [
    'demo_enabled' => in_array(env('APP_ENV', 'production'), ['local', 'testing'], true),
];
