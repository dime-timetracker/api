# Dime Timetracker

API provider written php.

## API call
```
Request   Authorization   Tranformation    Validation     Database
 >o-------------o--------------o---------------o------------>|
                |              |               |             |
                | Error        | Error         | Error       o
                V              V               V             |
 <o-------------o--------------o---------------o------------<|
Response
```

## Authentication call - Login
```
Request   Tranformation    Validation     Token-Generation
 >o-------------o--------------o--------------->|
                |              |                |
                | Errpr        | Error          o---> Database
                V              V                |
 <o-------------o--------------o---------------<|
Response
```