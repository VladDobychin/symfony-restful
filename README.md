# Symfony RESTful API Project

A Symfony-based RESTful API for managing players and teams.

## Setup

- Install dependencies:
  ```bash
  composer install
  ```

- Create database:
  ```bash
  php bin/console doctrine:database:create
  ```

- Run migrations:
    ```bash
  php bin/console doctrine:migrations:migrate
  ```

- Load dummy data (teams and players):
    ```bash
    php bin/console doctrine:fixtures:load  
    ```
- Running tests:

  ```bash
  php bin/phpunit
   ```

- Running specific tests: 
  ```bash
  php bin/phpunit tests/Unit/Service/PlayerServiceTest.php
  ```