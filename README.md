<p align="center"><a href="https://www.opecsis.com.br" target="_blank"><img src="https://app.opecsis.com.br/images/logos-opecsis/inteligente-300x100.png" width="300"></a></p>
<br>

## Laravel Test

Go to tests\Feature\Exam to check the list of tests that you will have to make it pass, in this sequence.

1. MigrationTest
2. ModelTest
3. CRUDTest
4. PackageInstallTest

Don't worry if you can't complete all tests.

<br>
<br>

## How to use

Run:

    git clone git@github.com:opecsis/laravel-test.git
    cd laravel-test/
    touch database/database.sqlite
    composer update
    cp .env.example .env
    php artisan key:generate
    php artisan migrate
    ./vendor/bin/phpunit tests/Feature/Exam/<TestFile>

<br>
<br>

Good Luck!
