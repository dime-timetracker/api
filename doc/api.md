# API Documentation

| Legend | Type | Description |
|---|---|---|
| {resource} | URI part | The item name |
| {id} | URI part | The item identifier |
| with | URI GET parameter | Define the amount of items |
| page | URI GET parameter | Define the page number |

## GET /api/{resource}

### Response - 200 OK

```
X-Dime-Total: COUNT
Link: </api/{resource}?with=20&page=2>; rel="next", </api/{resource}?with=20&page=2>; rel="last"
[
 {
   id: 1,
   name: "NAME"
   ...
 },
 ...
]
```

### Response - 404 Not Found

```
{
  "message": "Not found"
}
```

## GET /api/{resource}/{id}

### Response - 200 OK

```
{
 id: 1,
 name: "NAME"
 ...
}
```

### Response - 404 Not Found

```
{
  "message": "Not found"
}
```

## POST /api/{resource}

### Request data

```
{
 name: "NAME"
 ...
}
```

### Response - 200 OK

```
{
 id: 1,
 name: "NAME"
 ...
}
```

### Response - 404 Not Found

```
{
  "message": "Not found"
}
```

## PUT /api/{resource}/{id}

### Request data

```
{
 enabled: true
 ...
}
```

### Response - 200 OK

```
{
 id: 1,
 name: "NAME"
 ...
}
```

### Response - 404 Not Found

```
{
  "message": "Not found"
}
```

## DELETE /api/{resource}/{id}

### Response - 200 OK

```
{
 id: 1,
 name: "NAME"
 ...
}
```

### Response - 404 Not Found

```
{
  "message": "Not found"
}
```
