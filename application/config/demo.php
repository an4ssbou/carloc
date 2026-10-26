<?php
return ['enabled'=>filter_var(env('DEMO_MODE',false),FILTER_VALIDATE_BOOLEAN),'allow_local'=>filter_var(env('DEMO_ALLOW_LOCAL_HTTP',false),FILTER_VALIDATE_BOOLEAN)];
