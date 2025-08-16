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

6.  **External Nginx Configuration (Example for `reading-list.test`):**

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

7.  **Trust SSL Certificate (for local HTTPS):**
    
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
NETWORK_NAME=docker_redmine docker-compose up -d

# Or export for session
export NETWORK_NAME=docker_my_external_network
docker-compose up -d
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
