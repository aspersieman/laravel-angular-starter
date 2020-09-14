# README

Created by: Nicol van der Merwe | nicolvandermerwe@gmail.com | nicolvandermerwe.com

## About

Andes is a tool to help you set up and run recurring tasks. It can either ping a URL or execute a command on disk. It provides a mechanism to notify you if any tasks have failed (currently email only).

It's built using the following technologies:
 - PHP 7.0
 - Laravel
 - Beanstalk
 - Supervisor

It is recommended to use the homestead box to run this or to install it on a compatible Linux distro.

## Installation

1. Install Vagrant and VirtualBox 6.x
2. Clone the repo
3. Change to the andes folder and run `vagrant up`
4. Add the following to your hosts file:
  - 192.168.10.10  andes.test
  - 192.168.10.10  adminer.test
5. Copy the .env.example file to .env
6. Once the vagrant up command has finished, ssh into it: `vagrant ssh`
7. Navigate to the project folder: `cd /home/vagrant/code`
8. Run the following commands:
  - `sudo php artisan key:generate`
  - `sudo php artisan migrate`
  - `sudo php artisan db:seed `
  - `sudo php artisan passport:install`
9. If you are installing this for production reasons, truncate the task and queue tables

Now you will be able to setup tasks to be executed. Use the PostMan collection in the postman directory to start setting them up.

## NOTES 
 - By default, if you run the db seeder it will create a task that will execute the "test-mail.php" script every minute. This script sends out a test email to dummy email accounts
 - The username and password for the database is homestead / secret.
 - The log files used by Andes can be found in the storage/logs folder
 - Consider installing a mail-catching tool like MailCatcher to see the email notifications being sent by Andes
