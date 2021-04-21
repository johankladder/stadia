# Stadia Project for Planty.io

### Package description
This package contains all the sources for the stadia project. The main focus of this project is to offer support for 
using plants and stadia in combination with multiple country support. 

### Installation
Run the following command: `composer require johankladder/stadia` and following the instructions.

### Migration of the database
After installation run `php artisan migrate`

### Seed countries and climate codes
After migrating the database, please run the following commands to publish the seeders: `php artisan vendor:publish --tag=stadia-country-seeds`
and perform a `composer dump-autoload` to reload the newly added resources.

Run the seeder by the following command: `php artisan db:seed --class=CountriesTableSeeder`

### Sync existing plants and levels
In your application you can now access the backend of the stadia environment. Go to `/stadia` to see it. Before you 
can actual use it, synchronise the existing plants into stadia. Please use the 'sync' button provided in the overview 
to initialise the models.

After that you can fill in date ranges and climate relative information about these plants.

### Usage in general


