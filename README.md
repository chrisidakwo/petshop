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

- Create a test database Sqlite file using `touch database/database.sqlite` 
- Confirm the application is working as expected by running all tests using `php artisan test`

### 4. JWT and authentication

You need a private key, public key, and a paraphrase secret in order to authenticate with JWT. 
- Generate private key. Use this command to generate a private key: `openssl genpkey -algorithm RSA -out private_key.pem -pkeyopt rsa_keygen_bits:2048`
- Generate public key. Use this command to generate a public key: `openssl rsa -pubout -in private_key.pem -out public_key.pem`
- Generate secret: Use this command to generate a secret paraphrase: `php artisan jwt:secret`

The private and public keys are located in the base directory of the application. To have these files in a different location, `cd` into the directory and run the above first and second commands. Do not forget to update the paths in the config: `auth.jwt.public_key` and `auth.jwt.private_key`
