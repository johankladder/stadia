# Stadia Project for Planty.io

### Package description
This package contains all the sources for the stadia project. The main focus of this project is to offer support for 
using plants and stadia in combination with multiple country support. 

### Installation
Run the following command: `composer require johankladder/stadia` and following the instructions.

And publish the assets:
`php artisan vendor:publish --tag=public --force`
 
### Migration of the database
After installation run `php artisan migrate`

### Seed countries and climate codes
After migrating the database, please run the following commands to publish the seeders: `php artisan vendor:publish --tag=stadia-seeds`
and perform a `composer dump-autoload` to reload the newly added resources.

Also publish the needed datasets after the migration for stadia with the following command:
- `php artisan vendor:publish --tag=stadia-datasets`

Run the seeders by the following commands: 
- `php artisan db:seed --class=JohanKladder\\Stadia\\Database\\Seeds\\CountriesTableSeeder`
- `php artisan db:seed --class=JohanKladder\\Stadia\\Database\\Seeds\\ClimateCodesTableSeeder`
- `php artisan db:seed --class=JohanKladder\\Stadia\\Database\\Seeds\\KoepenLocationTableSeeder`

### Sync existing plants and levels
In your application you can now access the backend of the stadia environment. Go to `/stadia` to see it. Before you 
can actual use it, synchronise the existing plants into stadia. Please use the 'sync' button provided in the overview 
to initialise the models.

After that you can fill in date ranges and climate relative information about these plants.

### Usage in general
#### Configuration
The Stadia environment will be available at the `/stadia` prefix and using only the `web` middleware. To customize this you can publish 
the config file with the following command: 
`php artisan vendor:publish --provider="JohanKladder\Stadia\StadiaPackageServiceProvider" --tag="config"`. 
After that the `stadia.php` config file is located in your `/config` folder and can be changed accordingly. An example of this 
file is:

```php
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
```





