🚀 Getting Started with Docker
This guide will walk you through setting up the Laravel project using Docker.

Step 1: Clone the Repository
First, clone the project repository from your version control system.

git clone <your-repository-url>

Step 2: Start the Docker Containers
Navigate into the cloned directory and bring up the Docker containers. The --build flag ensures that the images are built from scratch, and -d runs the containers in the background.

docker-compose up --build -d

Step 3: Configure the Environment
Copy the example environment file and update the database configuration.

cp .env.example .env

Open the newly created .env file and set the following database variables for your Docker setup:

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=questproxy
DB_USERNAME=root
DB_PASSWORD=

Note: The DB_HOST is set to mysql because that is the service name defined in the docker-compose.yml file.

Step 4: Run Migrations and Seeders
Now, you can run your database migrations and seed the database with initial data. There are two ways to do this.

Option A: Running Commands from the Host Machine
Execute the commands directly using docker-compose exec:

# Run migrations
docker-compose exec app php artisan migrate

# Seed the database
docker-compose exec app php artisan db:seed

Option B: Running Commands from within the Container
Navigate into the backend directory if necessary:

cd backend/

Connect to the app container's shell:

docker-compose exec app bash

Once inside the container, run the standard Artisan commands:

php artisan migrate
php artisan db:seed
