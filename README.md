<h1>
   <img src="public/assets/img/logo/logoW.svg" alt="Tesserarius Logo" style="height: 48px; width: auto;">
   Tesserarius
</h1>

![PHP](https://img.shields.io/badge/php-777BB4?style=for-the-badge&logo=php&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/postgresql-4169E1?style=for-the-badge&logo=postgresql&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/tailwindcss-38B2AC.svg?style=for-the-badge&logo=tailwind-css&logoColor=white)
![Docker](https://img.shields.io/badge/docker-0db7ed.svg?style=for-the-badge&logo=docker&logoColor=white)<br>

# PROJECT IS WORK IN PROGRESS

---

## Prerequisites & Running

### Running
1. Install **Docker CLI** & **Docker Compose** on your system.
2. Clone the repository.
3. Navigate to the project directory.
4. Start the project:
   ```bash
   docker-compose up -d
   
### Building and Running
1. Install **Docker CLI** & **Docker Compose** on your system.
2. Clone the repository.
3. Navigate to the project directory.
4. Run npm to build assets:
   ```bash
   npm install
   npm run build
   ```
5. Run composer to install PHP dependencies:
   ```bash
   composer install
   ```
6. Start the project:
   ```bash
   docker-compose up -d

## Usage

- Website: http://localhost/, first signup a user.
- pgAdmin: http://localhost:8080, use credentials found in `compose.yml -> pgadmin`:
    - **Email Address:** admin@local.dev
    - **Password:** admin123

## Stop / Cleanup

1. Stop containers:
   ```bash
   docker-compose stop
2. Remove containers and named volume _(tesserarius_postgres_data)_:
    ```bash
   docker-compose down -v