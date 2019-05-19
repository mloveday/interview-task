# Interview task book API
## First Setup
### Dev machine Requirements
Install the following:
* Vagrant
* VirtualBox

Optional extras for ease of development:
* PHP 7.2
* Composer

Note that this allows installing dependencies outside of the vagrant box.

### Vagrant box setup
* Navigate to `./vagrant`, run `vagrant up`
* SSH into the vagrant box (`vagrant ssh`)
* Navigate to `/vagrant/code/source`
* Install dependencies: `composer install`
* Run DB migrations: `php bin/console doctrine:migrations:migrate`
* Load fixtures: `php bin/console doctrine:fixtures:load --append`

## Subsequent running
* Navigate to `./vagrant`, run `vagrant up`
* Endpoints available at `http://localhost:8000` (e.g. `http://localhost:8000/books?api_key=test_api_key`)
* dev & test environment api key for local requests located in `App\DataFixtures\AppFixtures` class

## Tests
* Run vagrant (`vagrant up`)
* SSH into vagrant (`vagrant ssh`)
* Navigate to `/vagrant/code/source`
* Run `./vendor/bin/simple-phpunit`
* Code coverage output available in `./tests/_reports`
* To increase test speed, remove/comment out the `<logging>..</logging>` element from `phpunit.xml.dist`.
    * Do not commit this change unless agreed
    * My machine takes ~5s without logging compared to ~18s

## Potentially helpful extras
* FTP to vagrant box via port 2222 using user/pass of `vagrant`/`vagrant`
* MySQL instance available via port 33060