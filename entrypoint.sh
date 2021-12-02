rm -f composer.lock
composer require orchestra/testbench:${TESTBENCH} --no-update --no-interaction --dev
composer install --no-interaction
composer show | grep laravel
php --version
vendor/bin/phpunit
