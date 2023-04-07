# Petshop

Buckhill Backend Developer Task 

## How to get started

### 1. Install dependencies

Install composer dependencies using `composer install`

### 2. Setup application

- Set application environment by copying from `.env.example` using `cp .env.example .env`
- Generate application key using `php artisan key:generate`

### 2. Setup database and migrations

- Create the database on local database server. Database name is `petshop` as defined in the `.env`
- Migrate and seed database tables using `php artisan migrate --seed`

### 3. Confirm tests

- Confirm the application is working as expected by running all tests using `php artisan test`

### 4. JWT and authentication

You need a private key, public key, and a paraphrase secret in order to authenticate with JWT. 
- Generate private key. Use this command to generate a private key: `openssl genpkey -algorithm RSA -out private_key.pem -pkeyopt rsa_keygen_bits:2048`
- Generate public key. Use this command to generate a public key: `openssl rsa -pubout -in private_key.pem -out public_key.pem`
- Generate secret: Use this command to generate a secret paraphrase: `php artisan jwt:secret`

The private and public keys are located in the base directory of the application. To have these files in a different location, `cd` into the directory and run the above first and second commands. Do not forget to update the paths in the config: `auth.jwt.public_key` and `auth.jwt.private_key`

### 5. Swagger Documentation

A Swagger documentation for the application API has been generated, and is available in the public directory as `api-docs.json`.

However, to generate a new documentation, you can use the command: `php artisan l5-swagger:generate`. The generated file can be found in the public directory.

To view the Swagger documentation, visit: `http:localhost:9900/api/documentation`. Where `http://localhost:9900` is the value of your `L5_SWAGGER_CONST_HOST` env config.

Visit `http:localhost:9900/api/docs` to access the parsed swagger annotations in JSON format
