# Dime Timetracker

API provider written php.

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