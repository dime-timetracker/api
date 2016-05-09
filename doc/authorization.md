# Authorization

## Authorize a request

To authorize a request you have to send the **Authorization** field with the request header.

```
GET /api/activity HTTP/1.1
Accept: application/json
Authorization: DimeTimetracker USERNAME,CLIENT-IDENTIFIER,TOKEN
```

| Parameter | Example | Description |
|-|-|-|
| Realm | DimeTimetracker | Defines the authorization type. |
| USERNAME | user-x | String, the username |
| CLIENT-IDENTIFIER | 74738ff5-5367-5958-9aee-98fffdcd1876 | String, must be unique, because every client has its own access token. |
| TOKEN | wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY | String, has to be requested (as described in `Request access token`_ ) |

## Login - Request access token

Authenticate user and generate access token.

### Request
* Resource: /login
* Method: POST
* Headers
``` http
Accept: application/json
Content-Type: application/json; charset=utf-8
```
* Body
``` javascript
{
   "user": "USERNAME",
   "password": "PASSWORD",
   "client": "CLIENT-IDENTIFIER"
}
```

### Success
* Status: 200
* Headers
``` http
Content-Type: application/json; charset=utf-8
Content-Length: <length>
```
* Body
``` javascript
{
  "token": "TOKEN",
  "expires": "TIMESTAMP"
}
```

### Failed
* Status: 403
* Headers
``` http
Content-Type: application/json
Content-Length: <length>
```
* Body
``` javascript
{
  "error": "Authentication error"
}
```

## Logout

Removes the access token. You have too authorize in the request to

### Request
* Resource: /logout
* Method: POST
* Headers
``` http
Accept: application/json
Authoriztion: DimeTimetracker USERNAME,CLIENT-IDENTIFIER,TOKEN
Content-Type: application/json
```
* Body
``` javascript
{
  "user": "USERNAME",
  "password": "PASSWORD",
  "client": "CLIENT-IDENTIFIER"
}
```

### Success
* Status: 200
* Headers
``` http
Content-Type: application/json
Content-Length: <length>
```
* Body
``` javascript
{
  "token": "TOKEN",
  "expires": "TIMESTAMP"
}
```

### Failed
* Status: 403
* Headers
``` http
Content-Type: application/json
Content-Length: <length>
```
* Body
``` javascript
{
  "error": "Authentication error"
}
```
