# Activities

An activity has a description which descrive what you are doing. It can be
assigned to a customer, a project or service. Every activity can have serveral
timeslices.

## GET /apidoc/activities

```
{
  "id": "integer",
  "project_id": "integer",
  "customer_id": "integer",
  "user_id": "integer",
  "service_id": "integer",
  "description": "text",
  "rate": "decimal",
  "rate_reference": "string",
  "created_at": "datetime",
  "updated_at": "datetime"
}
```

## Fields

## Filter - by[date]

The date filter with deliver all activities in this period.

* Format:
  * **YYYY-MM-DD** (ISO-8601)
  * **Y-m-d** (PHP)
  * yesterday, tommorrow, this-week, this-month, this-year, last-week, last-month, last-year, 4-weeks
* Possible structure
  * start
  ```
  by[date]=YYYY-MM-DD
  ```
  * start;end
  ```
  by[date]=YYYY-MM-DD;YYYY-MM-DD
  ```
  * ;end
  ```
  by[date]=;YYYY-MM-DD
  ```

* Examples
```
GET /api/activities?by[date]=2016-01-01
```
```
GET /api/activities?by[date]=2016-01-01;2016-01-31
```

## Filter - by[search]

The search filter do a like on description field.

* Format: Numeric string
* Possible structure
  ```
  by[search]=foo*
  ```
  ```
  by[search]=*foo*
  ```
  ```
  by[search]=fo*o*
  ```

## Filter - by[{relation}]

The cusomter filter will find activities with certain relation.

* Format: Numeric or numeric list
* Possible structure
  * relation with id 1
  ```
  by[{relation}]=1
  ```
  * relation with id 1 and 2
  ```
  by[{relation}]=1;2
  ```
  * all relations but not with id 2
  ```
  by[{relation}]=-2
  ```
* Relations can be
  * customer
  * project
  * service
  * tag
