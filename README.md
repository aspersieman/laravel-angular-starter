# README

Created by: Nicol van der Merwe | nicolvandermerwe@gmail.com | nicolvandermerwe.com

## About

This project serves to be a basic template with the following:
 - Laravel 7.x
 - Angular 10.1.x
 - Passort (OAuth)

## Requirements
 - Apache2 (other web servers will work, but no instructions for them are included)
 - Mysql/MariaDB
 - PHP 7.2x

## Installation 

1. Clone repo:
  git clone git@github.com:aspersieman/laravel-angular-starter.git
2. Navigate to code root folder
3. php composer.phar install
4. Install the apache vhost: located in "apache/laravel-angular-starter.conf"
5. Install .env file:
  cp .env.example .env # Ensure you set the database credentials correctly
6. Generate application encryption key
  php artisan key:generate
7. Create database
  mysql --user=root -e "CREATE DATABASE laravel_angular_starter"
8. Run migrations:
  php artisan migrate
9. Install passport:
  php artisan passport:install
10. Seed the database with an admin user:
  php artisan db:seed
  
  The user's credentials will be:
  email: admin@laravel-angular-starter.vagrant
  password: admin1234
11. From the output of the above command, copy the "Client secret" from "Client ID: 2" to the .env to the "OAUTH_CLIENT_SECRET" value, e.g:
  OAUTH_CLIENT_SECRET=TKJSGW1whQUtE3NwsokAx8oL5P3LeiZOFrZNjvJ7
12. install npm:
  sudo apt remove nodejs
  curl -o- https://raw.githubusercontent.com/creationix/nvm/v0.33.8/install.sh | bash
  export NVM_DIR="$HOME/.nvm"
  [ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"  # This loads nvm
  [ -s "$NVM_DIR/bash_completion" ] && \. "$NVM_DIR/bash_completion"  # This loads nvm bash_completion
  nvm install --lts
  nvm use --lts
13. install angular/cli:
  npm install -g @angular/cli
14. Build the spa code:
  - for production:
    - npm run build
  - for development:
    - npm run watch
15. Nagivate to the front end. You should see the angular landing page
