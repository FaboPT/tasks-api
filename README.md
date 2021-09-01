# Tasks App

## Requirements

- [docker](https://www.docker.com/products/docker-desktop)

## Info
- [Laravel 8 Info](https://laravel.com/docs/8.x/installation)

## Installation/Configuration

### Install dependencies
#### MAC OS / Linux
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



