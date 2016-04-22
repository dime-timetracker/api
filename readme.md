# Dime Timetracker

API provider written php.


## Install

Copy config file and edit database entry. Create database.

```
cp config/parameters.php.dist config/parameters.php
```

Install dime via console.

```
php console.php dime:install
```

## API call
```
Request   Authorization   ResourceType    
 >o-------------o--------------o------------>|
                |              |             |
                | Error        | Error       o Endpoint < -- > Repository
                V              V             |
 <o-------------o--------------o------------<|
Response
```

## Authentication call - Login
```
Request
 >o------------>|
                |
                o Endpoint < -- > Repository
                |
 <o------------<|
Response
```

## Authentication call - Logout
```
Request   Authorization
 >o-------------o------------>|
                |             |
                | Error       o Endpoint < -- > Repository
                V             |
 <o-------------o------------<|
Response
```
