=============
Authorization
=============

Authorize a request
===================

To authorize a request you have to send the **Authorization** field with the request header.

.. code-block:: http

  GET /api/activity HTTP/1.1
  Accept: application/json
  Authorization: DimeTimetracker USERNAME,CLIENT-IDENTIFIER,TOKEN

.. list-table:: Authorization
  :header-rows: 1

  * - Parameter
    - Example
    - Description
  * - Realm
    - DimeTimetracker
    - Defines the authorization type.
  * - USERNAME
    - admin
    - String, the username
  * - CLIENT-IDENTIFIER
    - 74738ff5-5367-5958-9aee-98fffdcd1876
    - String, must be unique, because every client has its own access token
  * - TOKEN
    - wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY
    - String, has to be requested (as described in `Request access token`_ )


Request access token
====================

Authenticate user and generate access token.

Request
-------

.. list-table:: Request
  :widths: 1 20
  :stub-columns: 1

  * - Resource:
    - /login
  * - Method:
    - POST
  * - Headers:
    - .. code-block:: http
      
        Accept: application/json
        Content-Type: application/json; charset=utf-8
      
  * - Body:
    - .. code-block:: javascript
    
        {
          "user": "USERNAME",
          "password": "PASSWORD",
          "client": "CLIENT-IDENTIFIER"
        }
        

Success
-------

.. list-table:: Response
  :widths: 1 20
  :stub-columns: 1

  * - Status:
    - 200
  * - Headers:
    - .. code-block:: http
      
        Content-Type: application/json
        Content-Length: <length>
  * - Body:
    - .. code-block:: javascript

        {
          "token": "TOKEN",
          "expires": "TIMESTAMP"
        }

Failed
------

.. list-table:: Response
  :widths: 1 20
  :stub-columns: 1

  * - Status:
    - 403
  * - Headers:
    - .. code-block:: http
        
        Content-Type: application/json
        Content-Length: <length>
  * - Body:
    - .. code-block:: javascript

        {
          "error": "Authentication error"
        }


Logout
======

Removes the access token. You have too authorize in the request to 

Request
-------

.. list-table:: Request
  :widths: 1 20
  :stub-columns: 1

  * - Resource:
    - /logout
  * - Method:
    - POST
  * - Headers:
    - .. code-block:: http
        
        Accept: application/json
        Authoriztion: DimeTimetracker USERNAME,CLIENT-IDENTIFIER,TOKEN
        Content-Type: application/json
        
  * - Body:
    - .. code-block:: javascript
    
        {
          "user": "USERNAME",
          "password": "PASSWORD",
          "client": "CLIENT-IDENTIFIER"
        }
        

Success
-------

.. list-table:: Response
  :widths: 1 20
  :stub-columns: 1

  * - Status:
    - 200
  * - Headers:
    - .. code-block:: http
        
        Content-Type: application/json
        Content-Length: <length>
        
  * - Body:
    - .. code-block:: javascript

        {
          "token": "TOKEN",
          "expires": "TIMESTAMP"
        }

Failed
------

.. list-table:: Response
  :widths: 1 20
  :stub-columns: 1

  * - Status:
    - 403
  * - Headers:
    - .. code-block:: http
        
        Content-Type: application/json
        Content-Length: <length>
        
  * - Body:
    - .. code-block:: javascript

        {
          "error": "Authentication error"
        }

