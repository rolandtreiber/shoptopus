<?php

return [

    'middleware' => ['role:super_admin'],
    'prefix' => 'io',
    'ignored_relationships' => ['audits'],
    'model_namespace' => 'App\\Models',
    'languages' => ['en', 'fr', 'de']

];
