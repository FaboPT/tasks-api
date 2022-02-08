# Tasks App

## Requirements

- [Docker](https://www.docker.com/products/docker-desktop)
- [Postman](https://www.postman.com/downloads/)

## Info

- [Laravel 9 Info](https://laravel.com/docs/9.x/installation)

## Installation/Configuration

### Install dependencies

#### macOS / Linux

```
docker run --rm -v $(pwd):/app composer install
```

#### Windows (Git Bash)

```
docker run --rm -v /$(pwd):/app composer install
```

#### Windows (Powershell)

```
docker run --rm -v ${PWD}:/app composer install
```

### Copy all file .env.example to .env

In terminal if you use macOS / Linux / Git Bash(Windows)

```
cp .env.example .env
```

Change database configurations in **.env**

```
DB_CONNECTION=mysql
DB_HOST=mysql_tasks
DB_PORT=3306
DB_DATABASE=yourdatabasename
DB_USERNAME=root
DB_PASSWORD=yourpassword
```

### Configure PHPUnit file

change value line **25** phpunit.xml in **phpunit.xml**

```
<server name="DB_DATABASE" value="yourdatabasename"/>
```

### Detach the application

```
docker-compose up -d
```

### Generate APP Key

```
docker-compose exec app php artisan key:generate
```

### Run the migrations and seed script

```
docker-compose exec app php artisan migrate --seed
```

### URL http://localhost:8000

### Login

- Go your database and seed the fake users created and choose one
- Password for users -> **password**

#### Create a Token

- **POST** - http://localhost:8000/api/login

#### TASKS Endpoints

- **GET** http://localhost:8000/api/tasks -> Get tasks
- **POST** http://localhost:8000/api/tasks -> Create a new task
- **PUT** http://localhost:8000/api/tasks/{id} -> Update task
- **DELETE** http://localhost:8000/api/tasks/{id} -> Delete task
- **PUT** http://localhost:8000/api/set-performed/{id} -> Performed the task

## Production

### URL https://prod-tasks-api.herokuapp.com

### Login

- user-manager@example.com | user-technician@example.com
- Password for users -> **password**

#### Create a Token

- **POST** - https://prod-tasks-api.herokuapp.com/api/login

#### TASKS Endpoints

- **GET** https://prod-tasks-api.herokuapp.com/api/tasks -> Get tasks
- **POST** https://prod-tasks-api.herokuapp.com/api/tasks -> Create a new task
- **PUT** https://prod-tasks-api.herokuapp.com/api/tasks/{id} -> Update task
- **DELETE** https://prod-tasks-api.herokuapp.com/api/tasks/{id} -> Delete task
- **PUT** https://prod-tasks-api.herokuapp.com/api/set-performed/{id} -> Performed the task



