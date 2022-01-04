rm -f composer.lock
composer require orchestra/testbench:${TESTBENCH_VERSION} --no-update --no-interaction --dev

if [ ! -z "$PHPUNIT_VERSION" ]; then
    composer require phpunit/phpunit:${PHPUNIT_VERSION} --no-update --no-interaction --dev
fi

if [ ! -z "$FAKER_VERSION" ]; then
    composer require fzaninotto/faker:${FAKER_VERSION} --no-update --no-interaction --dev
fi

composer install --no-interaction --no-progress --no-ansi
echo ""
composer show | grep laravel
echo ""
php --version
echo ""
curl --version
echo ""
vendor/bin/phpunit
