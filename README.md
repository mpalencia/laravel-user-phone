# laravel-user-phone
Simple RESTful API which stores phone numbers of a user.
  
Phone number function is based on the [PHP port](https://github.com/giggsey/libphonenumber-for-php) of [Google's libphonenumber API](https://github.com/googlei18n/libphonenumber) by [giggsey](https://github.com/giggsey).  
  
REST API with Package [Fractal](https://fractal.thephpleague.com/)

## Assumptions

- Client is another entity like a user  
- Client registration is publicly accessible and anyone can register  
- Clients has 2 types, authorized and not authorized  
- Users has 2 roles, admin and non-admin  
- Admin type of user and authorize client are the only authorize to create new users  
- Admin type of user can fully access the client's CRUD resources  
- For this demo I just used the same DB on testing, but on development we separate testing DB from development DB

## Built using  
Laravel 5.7.3  
Php 7.2  
Mysql 5.6  
Phpunit 7.3  
  
## Installation
- Install Composer
- Download the repository and unzip into your server
- Change .env for database connection
- Run <code>composer update</code>
- Run <code>php artisan migrate</code>  
- Run <code>db:seed</code>  
- Run <code>php artisan serve</code>  
  
## Route List
  
To check list via terminal. Run <code>php artisan route:list</code>  
  
* Get api_token via client registration or use super admin api_token (Run 'php artisan db:seed' to create super admin)  
* On testing via Postman, please add on the header - key: Accept, value: application/json http://prntscr.com/kvfj71

// User  
Create User : http://localhost:8000/api/user-create (POST)  
Update User : http://localhost:8000/api/user-update/{id} (PUT)  
Delete User : http://localhost:8000/api/user-delete/{id} (DELETE)  
Show User : http://localhost:8000/api/user/{id} (GET)  
  
// Client  
Create User : http://localhost:8000/api/client-create (POST)  
Update User : http://localhost:8000/api/client-update/{id} (PUT)  
Delete User : http://localhost:8000/api/client-delete/{id} (DELETE)  
Show User : http://localhost:8000/api/client/{id} (GET)  
  
// User Phone  
Create User Phone : http://localhost:8000/api/phone-create (POST)  
Update User Phone : http://localhost:8000/api/phone-update/{id} (PUT)  
Delete User Phone : http://localhost:8000/api/phone-delete/{id} (DELETE)  
Show User Phone : http://localhost:8000/api/phone/{id} (GET)  
Show All User Phone : http://localhost:8000/api/phones/{user_id} (GET)  
  



