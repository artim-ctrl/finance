# Finance: Project with Docker, Frontend, and Backend in Go

## Description

This project uses Docker Compose to manage both frontend and backend services.
The frontend is built with TypeScript, and the backend is implemented in Go.

## Makefile Commands

Use the following Makefile commands to manage the project:

### 1. Start Services
```sh
make up
```
Starts all services in detached mode using Docker Compose.

### 2. Stop Services
```sh
make down
```
Stops and removes all running containers.

### 3. Code Linting (ESLint)
```sh
make eslint
```
Runs ESLint to check the frontend code.

### 4. Code Formatting (Prettier)
```sh
make prettier
```
Formats the frontend code using Prettier.

### 5. Build Frontend
```sh
make yarn-build
```
Builds the frontend application.

### 6. Create Database Migrations
```sh
make migration name=migration_name
```
Generates database migration files in the `migrations` directory. Replace `migration_name` with the desired name.

- Two files will be created:
    - `<timestamp>_migration_name.down.sql`
    - `<timestamp>_migration_name.up.sql`

## Project Structure
- **frontend/**: Frontend application files.
- **internal/**: Backend application written in Go.
- **migrations/**: SQL scripts for managing database migrations.

## Dependencies

Ensure the following dependencies are installed:
- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)
- [Make](https://www.gnu.org/software/make/)

## Getting Started
1. Ensure all dependencies are installed.
2. Build and start the project:
   ```sh
   make up
   ```
3. To stop the project, use:
   ```sh
   make down
   ```
