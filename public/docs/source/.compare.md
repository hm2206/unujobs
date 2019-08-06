---
title: API Reference

language_tabs:
- bash
- javascript

includes:

search: true

toc_footers:
- <a href='http://github.com/mpociot/documentarian'>Documentation Powered by Documentarian</a>
---
<!-- START_INFO -->
# Info

Welcome to the generated API reference.
[Get Postman Collection](http://localhost/docs/collection.json)

<!-- END_INFO -->

#general
<!-- START_ef71ae8252e74a5351ab1442f26edc44 -->
## Muetra una lista de las recursos

> Example request:

```bash
curl -X GET -G "/api/v1/planilla" 
```

```javascript
const url = new URL("/api/v1/planilla");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
[
    {
        "id": 1,
        "key": "01",
        "descripcion": "Planilla Activos",
        "created_at": "2019-07-25 03:15:09",
        "updated_at": "2019-07-25 03:15:09"
    },
    {
        "id": 2,
        "key": "05",
        "descripcion": "Planilla Cas",
        "created_at": "2019-07-25 03:15:10",
        "updated_at": "2019-07-25 03:15:10"
    },
    {
        "id": 3,
        "key": "06",
        "descripcion": "Planilla Adicional",
        "created_at": "2019-07-25 03:15:10",
        "updated_at": "2019-07-25 03:15:10"
    }
]
```

### HTTP Request
`GET api/v1/planilla`


<!-- END_ef71ae8252e74a5351ab1442f26edc44 -->

<!-- START_7ae6db779765bda37d1b1e1b810c25f7 -->
## Muetra un recurso específico

> Example request:

```bash
curl -X GET -G "/api/v1/planilla/1" 
```

```javascript
const url = new URL("/api/v1/planilla/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "id": 1,
    "key": "01",
    "descripcion": "Planilla Activos",
    "created_at": "2019-07-25 03:15:09",
    "updated_at": "2019-07-25 03:15:09",
    "cargos": [
        {
            "id": 1,
            "tag": "Contratado",
            "descripcion": "Docente Contratado",
            "planilla_id": 1,
            "type_categoria_id": 2,
            "created_at": "2019-07-24 13:25:31",
            "updated_at": "2019-07-24 13:25:31"
        },
        {
            "id": 2,
            "tag": "Nombrado",
            "descripcion": "Docente Nombrado",
            "planilla_id": 1,
            "type_categoria_id": 2,
            "created_at": "2019-07-24 13:28:15",
            "updated_at": "2019-07-24 13:28:15"
        },
        {
            "id": 3,
            "tag": "Contratado",
            "descripcion": "Administrativo Contratado",
            "planilla_id": 1,
            "type_categoria_id": 1,
            "created_at": "2019-07-24 13:28:29",
            "updated_at": "2019-07-24 13:28:29"
        },
        {
            "id": 4,
            "tag": "Nombrado",
            "descripcion": "Administrativo Nombrado",
            "planilla_id": 1,
            "type_categoria_id": 1,
            "created_at": "2019-07-24 13:29:04",
            "updated_at": "2019-07-24 13:29:04"
        }
    ]
}
```

### HTTP Request
`GET api/v1/planilla/{id}`


<!-- END_7ae6db779765bda37d1b1e1b810c25f7 -->

<!-- START_c78f05b44678f88ef1fa21202eededd1 -->
## Muetra un recurso específico

> Example request:

```bash
curl -X GET -G "/api/v1/cargo/1" 
```

```javascript
const url = new URL("/api/v1/cargo/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "id": 1,
    "tag": "Contratado",
    "descripcion": "Docente Contratado",
    "planilla_id": 1,
    "type_categoria_id": 2,
    "created_at": "2019-07-24 13:25:31",
    "updated_at": "2019-07-24 13:25:31",
    "categorias": [
        {
            "id": 1,
            "nombre": "DC A1",
            "monto": null,
            "created_at": "2019-07-24 13:23:55",
            "updated_at": "2019-07-24 14:22:00",
            "pivot": {
                "cargo_id": 1,
                "categoria_id": 1
            }
        },
        {
            "id": 2,
            "nombre": "DC A2",
            "monto": null,
            "created_at": "2019-07-24 14:22:20",
            "updated_at": "2019-07-24 14:22:33",
            "pivot": {
                "cargo_id": 1,
                "categoria_id": 2
            }
        },
        {
            "id": 3,
            "nombre": "DC A3",
            "monto": null,
            "created_at": "2019-07-24 14:22:46",
            "updated_at": "2019-07-24 14:22:46",
            "pivot": {
                "cargo_id": 1,
                "categoria_id": 3
            }
        },
        {
            "id": 4,
            "nombre": "DC B1",
            "monto": null,
            "created_at": "2019-07-24 14:23:06",
            "updated_at": "2019-07-24 14:23:06",
            "pivot": {
                "cargo_id": 1,
                "categoria_id": 4
            }
        },
        {
            "id": 5,
            "nombre": "DC B2",
            "monto": null,
            "created_at": "2019-07-24 14:23:13",
            "updated_at": "2019-07-24 14:23:13",
            "pivot": {
                "cargo_id": 1,
                "categoria_id": 5
            }
        },
        {
            "id": 6,
            "nombre": "DC B3",
            "monto": null,
            "created_at": "2019-07-24 14:23:20",
            "updated_at": "2019-07-24 14:23:20",
            "pivot": {
                "cargo_id": 1,
                "categoria_id": 6
            }
        }
    ]
}
```

### HTTP Request
`GET api/v1/cargo/{id}`


<!-- END_c78f05b44678f88ef1fa21202eededd1 -->

<!-- START_5c1a94f396cd6ba016fd7c8deaa6f517 -->
## Muetra una lista de recursos

> Example request:

```bash
curl -X GET -G "/api/v1/meta" 
```

```javascript
const url = new URL("/api/v1/meta");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
[
    {
        "id": 1,
        "metaID": "1",
        "meta": "Meta 1",
        "sectorID": "1",
        "sector": "H",
        "pliegoID": "1",
        "pliego": "1",
        "unidadID": "1",
        "unidad_ejecutora": "1",
        "programaID": "1",
        "programa": "1",
        "funcionID": "1",
        "funcion": "1",
        "subProgramaID": "1",
        "sub_programa": "1",
        "actividadID": "1",
        "actividad": "1",
        "created_at": "2018-01-01 00:00:00",
        "updated_at": "2019-08-05 11:49:51"
    }
]
```

### HTTP Request
`GET api/v1/meta`


<!-- END_5c1a94f396cd6ba016fd7c8deaa6f517 -->


