=============
Authorization
=============

To authorize the user too access the api you have to request an access token.

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
    - Accept: application/json
      
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
    - Content-Type: application/json
    
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
    - Content-Type: application/json
    
      Content-Length: <length>
  * - Body:
    - .. code-block:: javascript

        {
          "error": "Authentication error"
        }

