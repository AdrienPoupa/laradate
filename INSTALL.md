# Installation

## Requirements
* PHP >= 7
* A database such as MySQL/MariaDB

## Instructions
1. Install dependencies: `composer update`
1. Copy .env.example to .env
1. Generate an APP_KEY: `php artisan key:generate`
1. Fill .env with your information (database connection, application name etc)
1. Prepare the database: `php artisan migrate`
1. Fill /app/config/laradate.php with your information
1. Enjoy

You can login using the e-mail address you have set in /app/config/laradate.php as password. It is obviously extremely recommended to change your password to something more secure.