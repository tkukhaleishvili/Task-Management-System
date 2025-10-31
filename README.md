# Task Management System (PHP)

A simple PHP task management app built without frameworks. Follows a clean MVC structure and uses MySQL/MariaDB for storage.

## Features
- MVC organization (controllers, models, views)
- PDO (pdo_mysql) database access
- Lightweight, framework-free codebase
- Optional Composer autoloader

## Requirements
- PHP 8.0+
- pdo_mysql extension enabled
- MySQL or MariaDB
- Web server (Apache/Nginx) or PHP built-in server
- Composer (optional)

## Installation
1. Clone or download the repository:
    ```
    git clone https://github.com/yourusername/taskmanager.git
    cd taskmanager
    ```
    Or extract the ZIP into your web root (e.g., C:\xampp\htdocs\taskmanager or /var/www/html/taskmanager).

2. Create the database and import schema:
    ```
    mysql -u root -p taskmanager_db < database/schema.sql
    ```

3. Configure database credentials in `config/config.php`:
    ```php
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'taskmanager_db');
    define('DB_USER', 'root');
    define('DB_PASS', 'your_password');
    ```

4. (Optional) Rebuild autoloader:
    ```
    composer install
    ```

## Running the app
- Built-in PHP server (for development):
  ```
  php -S localhost:8000 -t public
  ```
  Open: http://localhost:8000

- Apache/Nginx:
  Point your virtual host / document root to the project's `public/` directory.

## Project structure
- app/ — controllers, models, views  
- config/ — config.php  
- core/ — App.php, Controller.php, Database.php  
- public/ — index.php, public assets  
- database/ — schema.sql (and sample data if included)

## Default credentials (if sample data present)
- Username: `your SQL user`  
- Password: `your SQL password`

## Troubleshooting
- Ensure PHP version ≥ 8 and pdo_mysql is enabled.
- Verify DB credentials and that the DB server is running.
- Check server logs or run PHP built-in server for visible errors.
- Confirm file permissions for your web server user.

## Notes
- This project is intended as a minimal example — modify authentication, validation, and security for production use.
