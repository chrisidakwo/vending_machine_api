## Vending Machine
Built with PHP 8.1

### How To Use
- Install composer dependencies `composer install`

- Copy `.env.example` into a new file `.env`. You can use this command: `cp .env.example .env`

- Generate the application key using: `php artisan key:generate`

- Generate JWT secret using: `php artisan jwt:secret`

- The application uses a SQLite file database for test, hence you will need to manually create the database file using this command: `touch database/database.sqlite`

- You can run all tests using `php artisan test`

- Run database migrations and seeds using `php artisan migrate --seed`
  - Two `buyer` accounts will be generated with associated products
    - **Buyer 1**
      - `Username`: buyer1578
      - `Password`: password
    - **Buyer 2**
      - `Username`: buyer2578
      - `Password`: password

- To seed products, use: `php artisan db:seed --class=ProductsTableSeeder`. Five (5) products will be seeded each time the command is called

- Run the application using: `php artisan serve --port 8500`. Please ensure to run it with the `--port` flag and the specified port number. That is because the frontend application will be expecting the API to run on port 8500 as defined in the `.env` for frontend. Of coursse, except maybe you reconfigure that on the frontend project.

- You're set to go! Thread carefully! ;)


#### NOTE
The React frontend project runs on localhost port 3000 and is available at: https://github.com/chrisidakwo/vending_machine_web
