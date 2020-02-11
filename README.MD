# Cover Manager test app

## Introduction

This is the backend of the application, built in Symfony 4 as a RESTFUL API.

Once installed, the API documentation can be found [here](http://localhost/cm_university/public/index.php/api/doc).

## Install

I will assume we're working in a Linux machine. The installation processes for Windows or Mac are very 
similar.

* Clone the repository

And get to the project directory.

* Install the dependencies

`composer install`

This will make the `vendor` directory appear.

* Create the database

Once the application is installed, create the database schema.

To do this, first edit the `.env` file setting the correct database name and credentials:

`DATABASE_URL=mysql://bb_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7`

Then, in console, run the script to create the schema:

`php bin/console doctrine:schema:create`

You can add test data by running the following command:

`php bin/console doctrine:fixtures:load`

And that's it. The API should now work.
