## Vending Machine


### How To Use
- Copy `.env.example` into a new file `.env`. You can use this command: `cp .env.example .env`

- Generate the application key using: `php artisan key:generate`

- Generate JWT secret using: `php artisan jwt:secret`

- The application uses a SQLite file database, hence you will need to manually create the database file using this command: `touch database/database.sqlite`

- Run database migrations and seeds using `php artisan migrate --seed`
  - Two `buyer` accounts will be generated with associated products
    - **Buyer 1**
      - `Username`: buyer1578
      - `Password`: password
    - **Buyer 2**
      - `Username`: buyer2578
      - `Password`: password

- Run the application using: `php artisan serve --port 8100`. Please ensure to run it with the `--port` flag and port number. That is because the frontend application will be expecting the API to run on that port. Except maybe you reconfigure that on the frontend project.

- You're set to go! Thread carefully! ;)
