rm -f composer.lock
composer require orchestra/testbench:${TESTBENCH} --no-update --no-interaction --dev
composer install --no-interaction
echo ""
composer show | grep laravel
echo ""
php --version
echo ""
curl --version
echo ""
vendor/bin/phpunit
