<?php

return [
    'prefix' => 'stadia',
    'middleware' => ['web'],
    'api-prefix' => 'stadia/api',
    'api-middleware' => ['api'],
    'plant_table_soft_deleted' => false,
    'plants_table_name' => 'plants',
    'plants_name_column' => 'name',
    'levels_table_name' => 'levels',
    'levels_name_column' => 'name',
];
