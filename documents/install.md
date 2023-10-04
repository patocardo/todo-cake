# Installation Guide for Memo Test Game

This guide provides step-by-step instructions for setting up the Memo Test Game on your local machine.

## Prerequisites

- Docker installed on your machine.
- Git installed on your machine.

## Steps

1. **Clone the Repository**: `git clone https://github.com/patocardo/todo-cake.git`
2. **Navigate to the Project Directory**: `cd todo-cake`
3. **Build Docker Container**: `docker-compose build`
4. **Pick-up Docker container**: `docker-compose up -d`
5. **Enter container bash**: `docker exec -it todocake-webserver` // prompt will change
6. **Install Backend Dependencies**: `composer install`
7. **Run Database Migrations**: `php artisan migrate`
8. **Move to the vue application**: `cd vue-app`
9. **Install Frontend Dependencies**: `npm install`
10. **Move to root path**: `cd ..`
11. **Run cake server**: `bin/cake server -H 0.0.0.0`

12. **Access the Application**:
Open your browser and navigate to `http://localhost:8765` to access the TODO app.

## Troubleshooting

If you encounter any issues during the installation process, please check the following:

- Ensure Docker is running and has the necessary permissions.
- Check the Docker logs for any errors: `docker-compose logs`.

### Common errors

Most of the errors are related to permissions for
* logging
* saving modified files
* creating files from CLI
* Even connection between frontend and backend have certain "connection errors" but they are docker-related

One thing to check, is the owner of files and/or folders where the permission error was triggered.
```
ls -l path/to/the/problem
docker exec -it todo-cake ls -l path/to/the/problem
```

If you're still facing issues, please raise an issue on the GitHub repository.







