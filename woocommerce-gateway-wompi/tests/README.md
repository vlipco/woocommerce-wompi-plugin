# WooCommerce Gateway Wompi Tests

This document discusses unit tests.

## Table of contents

- [WooCommerce Gateway Wompi Tests](#woocommerce-gateway-wompi-tests)
    - [Table of contents](#table-of-contents)
    - [Initial Setup](#initial-setup)
        - [MySQL database](#mysql-database)
        - [Setup instructions](#setup-instructions)
    - [Running Tests](#running-tests)

## Initial Setup

### MySQL database

To run the tests, you need to create a test database. You can:
- Access a database on a server
- Connect to your local database on your machine
- Use a solution like VVV - if you are using VVV you might need to `vagrant ssh` first
- Run a throwaway database in docker with this one-liner: `docker run --rm --name woocommerce_test_db -p 3306:3306 -e MYSQL_ROOT_PASSWORD=woocommerce_test_password -d mysql:5.7.33`. ( Use `tests/bin/install.sh woocommerce_tests root woocommerce_test_password 0.0.0.0` in next step)

### Setup instructions

Once you have database, from the WooCommerce Gateway Wompi root directory run the following:

1. Install dependencies [PHPUnit](http://phpunit.de/) and [WooCommerce](github.com/woocommerce/woocommerce/) via Composer by running:
    ```
    $ composer install
    ```

2. Install WordPress and the WP Unit Test lib using the `install-wp-tests.sh` script:
    ```
    $ tests/bin/install-wp-tests.sh <db-name> <db-user> <db-password> [db-host]
    ```

    You may need to quote strings with backslashes to prevent them from being processed by the shell or other programs.

    Example:

        $ tests/bin/install.sh woocommerce_tests root root

        #  woocommerce_tests is the database name and root is both the MySQL user and its password.

    **Important**: The `<db-name>` database will be created if it doesn't exist and all data will be removed during testing.


3. Install WooCommerce dependencies via Composer by running:
    ```
    $ cd vendor/woocommerce/woocommerce
    $ composer install
    ```
## Running Tests

Change to the plugin root directory and type:

    $ vendor/bin/phpunit
