# laravel-user-phone
Simple RESTful API which stores phone numbers of a user.
  
Phone number function is based on the [PHP port](https://github.com/giggsey/libphonenumber-for-php) of [Google's libphonenumber API](https://github.com/googlei18n/libphonenumber) by [giggsey](https://github.com/giggsey).  
  
REST API with Package [Fractal](https://fractal.thephpleague.com/)

## Specs  
Laravel 5.7.3  
Php 7.2  
Mysql 5.6  
phpunit 7.0  
  
## Installation

run php artisan migrate  
run db:seed  
run php artisan serve  
  
## Route List
  
To check list via terminal. Run 'php artisan route:list'  
  
* Get api_token via client registration or use super admin api_token (Run 'php artisan db:seed to create super admin')  
* On testing via Postman, please add on the header - key: Accept, value: application/json

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
  
## Description


## Assumptions

- Client is another entity like a user  
- Client registration is publicly accessible and anyone can register  
- Clients has 2 types, authorized and not authorized  
- Users has 2 roles, admin and non-admin  
- Admin type of user and authorize client are the only authorize to create new users  
- Admin type of user can fully access the client's CRUD resources  
  
Objective
To create a simple RESTful API which will store phone numbers of a user.

Purpose
To demonstrate the abilities of the developer by creating a simple RESTful API from scratch using his/her own preferred PHP framework. Specifically, we want to assess the skills of the developer in the following
areas:
• expertise in scaffolding a RESTful API 
• client authorization
• role-based access control
• traversing with pagination
• input validation
• unit & integration testing

The following will also be observed:
• adherence to best practices & design patterns
• clean code and readability

Requirements
Below are the API resources that should be created:
• Clients
• There should be an API endpoint for registering a new client, this will be publicly accessible to anyone
• A client can update its own info, but not other clients
• Admin has full access (CRUD) to this resource

• Users
• There will be two user roles: admin and non-admin
• Only authorized clients or the Admin will be able to create a new user but only the Admin can create a user with admin role
• A User can view or update his/her own info, but not other users
• Admin has full access (CRUD) to this resource

• Phone numbers
• A user can add phone numbers as much as he/she wants but duplicates shouldn’t be allowed, the phone number should also be
validated (use your own country’s format)
• A user can update his/her own phone number
• A user can view all his/her phone numbers
• Phone numbers should be limited to 10 items per response
• Response should contain info that clients can use to get the previous or next batch of phone numbers
• A user can delete a phone number
• Admin has full access (CRUD) to this resource

There should be a unit test in place for each API resource. And an integration test for the whole group. The developer can decide on how much test cases he wants to write and which tools to use. We suggest to keep everything simple and easy to understand though.

Important: Please make sure that the app runs without any error and has successfully passed all tests!

Please submit your answers via public Github repo.


GOOD LUCK!
