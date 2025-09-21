# Reading List

A web application for saving and organizing "read later" articles. A personal service to replace scattered browser bookmarks and text files for managing reading lists.

## Requirements

*   **Docker:** Ensure Docker Desktop or Docker Engine is installed and running on your system.
*   **Docker Compose:** Included with Docker Desktop, or install separately if using Docker Engine.
*   **Docker Network:** By default, the project uses an external network named `docker_external`. You can customize this (see Network Configuration below).
*   **External Nginx (Optional but Recommended for Production/Local Dev):** This setup assumes you have an external Nginx instance (or similar reverse proxy) that will forward requests to the `reading-list-nginx` service running within this project's Docker Compose stack.

## Setup and Deployment

Follow these steps to get "Reading List" up and running:

1.  **Clone the Repository:**
    ```bash
    git clone https://github.com/bjakushka/reading-list
    cd reading-list
    ```

2.  **Build and Run Docker Compose Services:**

    *NOTE*: see section on [Network Configuration](#network-configuration) below if you need to customize the network.

    From the project root directory, execute:
    ```bash
    docker compose up --build -d
    ```
    This will build the `laravel` image, start `nginx`, `laravel`.

3.  **Install Laravel Dependencies:**

    Once the containers are running, install the PHP dependencies:
    ```bash
    docker exec reading-list-laravel composer install
    ```

4.  **Environment Configuration:**

    Create a `.env` file for your Laravel application.
    ```bash
    cp laravel/.env.example laravel/.env
    ```
    Update `laravel/.env` with your desired settings. Ensure `APP_KEY` is set (next step).

5.  **Generate Laravel Application Key:**

    Once the `laravel` service is running, generate the application key:
    ```bash
    docker exec reading-list-laravel php artisan key:generate
    ```

6.  **Run Database Migrations:**

    Once the application key is generated, run the database migrations:
    ```bash
    docker exec reading-list-laravel php artisan migrate
    ```

7.  **Create First User (Optional):**

    To create your first user account, you can use the artisan command:
    ```bash
    docker exec reading-list-laravel php artisan user:create
    ```

8.  **External Nginx Configuration (Example for `reading-list.test`):**

    If you are using an external Nginx, configure it to proxy requests to the `reading-list-nginx` service.

    Example Nginx server block (adjust `proxy_pass` if your Docker network name is different or if you're not using an external network):

    ```nginx
    server {
        listen 80;
        listen 443 ssl;
        server_name reading-list.test;

        # SSL configuration (if using HTTPS)
        ssl_certificate /path/to/your/reading-list.test.crt;
        ssl_certificate_key /path/to/your/reading-list.test.key;

        location / {
            proxy_pass http://reading-list-nginx:80; # Or http://<reading_list_nginx_ip>:80 if not on same Docker network
            proxy_set_header Host $host;
            proxy_set_header X-Real-IP $remote_addr;
            proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
            proxy_set_header X-Forwarded-Proto $scheme;
        }
    }
    ```
    *NOTE:* For local development, you might need to add `reading-list.test` to your `/etc/hosts` file pointing to your Docker host's IP (e.g., `127.0.0.1 reading-list.test`).

9.  **Trust SSL Certificate (for local HTTPS):**
    
    If you generated a self-signed certificate for `reading-list.test`, you will need to trust it in your operating system's keychain to avoid browser warnings. (Specific steps vary by OS).

## Network Configuration

By default, this project uses an external Docker network named `docker_external`. You can customize the network name to match your existing infrastructure.

### Custom Network Configuration

The project connects to an existing external network (typically where your external nginx runs).

**Option 1: Using .env file (Recommended)**

Create or edit the `.env` file in the project root:
```bash
# Set your existing network name
NETWORK_NAME=your_existing_network_name
```

**Option 2: Using environment variable**

```bash
# Temporarily override network name
NETWORK_NAME=your_network_name docker compose up -d

# Or export for session
export NETWORK_NAME=docker_my_external_network
docker compose up -d
```

**Important:** The network must already exist (created by your external nginx setup). If you're unsure of the network name, list existing networks:
```bash
docker network ls
```

### Restarting External Nginx

After starting or restarting Reading List services, you may need to restart your external nginx to refresh DNS resolution:
```bash
docker restart your_external_nginx_container
```

## Testing

The project uses PHPUnit for testing with a comprehensive test suite covering feature and unit tests.

### Running Tests via Command Line

**Run all tests:**
```bash
docker exec reading-list-laravel composer test
```

**Alternative method:**
```bash
docker exec reading-list-laravel php artisan test
```

**Run specific test files:**
```bash
docker exec reading-list-laravel php artisan test tests/Feature/Http/Controllers/BookmarksController/IndexTest.php
```

**Run tests with coverage (if configured):**
```bash
docker exec reading-list-laravel php artisan test --coverage
```

### PhpStorm IDE Configuration

The key is using your Docker Compose service as the PHP interpreter so PhpStorm 
runs tests inside the container with the correct environment.

To run and debug tests directly from PhpStorm IDE:

#### 1. Configure PHP Interpreter for Docker

1. **File → Settings** (or **PhpStorm → Preferences** on macOS)
2. **PHP → CLI Interpreter**
3. Click **"+"** → **"From Docker, Vagrant, VM, WSL, Remote..."**
4. Select **Docker Compose**
5. Configuration files: `./docker-compose.yml`
6. Service: `laravel`
7. PHP executable path: `/usr/local/bin/php`
8. Click **OK**

#### 2. Set up PHPUnit Test Framework

1. **PHP → Test Frameworks**
2. Click **"+"** → **PHPUnit by Remote Interpreter**
3. Choose your Docker interpreter from step 1
4. PHPUnit library:
   - **Use Composer autoloader**: `/var/www/html/vendor/autoload.php`
   - **Path to PHPUnit**: should auto-detect as `/var/www/html/vendor/bin/phpunit`
5. Test Runner:
   - **Default configuration file**: `/var/www/html/phpunit.xml`
   - **Default bootstrap file**: `/var/www/html/vendor/autoload.php`

#### 3. Configure Run/Debug Configuration

1. **Run → Edit Configurations**
2. Click **"+"** → **PHPUnit**
3. Name: `Laravel Tests`
4. Test scope: **Defined in the configuration file**
5. Interpreter: Your Docker interpreter
6. Working directory: should auto-detect as `/var/www/html`

#### 4. Running Tests from IDE

- **Run all tests**: Right-click on `tests` folder → **Run 'tests'**
- **Run specific test**: Right-click on test file → **Run 'TestName'**
- **Run single method**: Click gutter icon next to test method
- **Debug tests**: Use **Debug** instead of **Run**

#### Additional Tips

- **Path mappings**: PhpStorm should auto-detect them, but verify in **PHP → Path Mappings**
- **Environment variables**: Set `APP_ENV=testing` in run configuration if needed
- **Xdebug**: Enable in your Docker PHP configuration for debugging support

## Code Quality

The project uses PHP CS Fixer for code formatting and PHPStan for static analysis.

### PHP CS Fixer Configuration

**Run code formatting:**
```bash
docker exec reading-list-laravel vendor/bin/php-cs-fixer fix
```

**Useful resources:**
- [PHP CS Fixer Configurator](https://mlocati.github.io/php-cs-fixer-configurator) - Interactive tool for creating custom rule configurations
- [Complete rules documentation](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/blob/master/doc/rules/index.rst) - Detailed reference for all available formatting rules
- [Official PHP CS Fixer website](https://cs.symfony.com/) - Main documentation and guides