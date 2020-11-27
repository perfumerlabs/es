This is microservice to store to and retrieve results from ElasticSearch.
Currently, it creates index by fulltext search strategy only.

Installation
============

```bash
docker run \
-p 80:80/tcp \
-e ES_HOST=elasticsearch
-e ES_PORT=9200
```

Environment variables
=====================

- ES_HOST - Elasticsearch instance url (without http://). Required.
- ES_PORT - Elasticsearch instance port. Default is 9200.

Volumes
=======

This image has no volumes.

If you want to make any additional configuration of container, mount your bash script to /opt/setup.sh. This script will be executed on container setup.

API Reference
=============

### Create an index

`POST /index`

Parameters (json):
- name [string,required] - name of the index.

Request example:

```json
{
    "name": "foobar"
}
```

Response example:

```json
{
    "status": true
}
```

### Delete an index

`DELETE /index`

Parameters (json):
- name [string,required] - name of the index.

Request example:

```json
{
    "name": "foobar"
}
```

Response example:

```json
{
    "status": true
}
```

### Create a document

`POST /document`

Request parameters (json):
- index [string,required] - name of the index.
- id [integer,required] - unique identity of inserted document.
- title [string,optional] - title of document.
- text [string,required] - text of document.
- locale [string,required] - locale of document.

Request example:

```json
{
    "index": "foobar",
    "id": 1,
    "locale": "en",
    "title": "Hello",
    "text": "World"
}
```

Response example:

```json
{
    "status": true
}
```

### Create multiple documents at once

`POST /documents`

Request parameters (json):
- index [string,required] - name of the index.
- documents [array,required] - array of documents to save.
- documents.id [integer,required] - unique identity of inserted document.
- documents.title [string,optional] - title of document.
- documents.text [string,required] - text of document.
- documents.locale [string,required] - locale of document.

Request example:

```json
{
    "index": "foobar",
    "documents": [
        {
            "id": 1,
            "locale": "en",
            "title": "Hello",
            "text": "World"
        }    
    ]
}
```

Response example:

```json
{
    "status": true
}
```

### Delete a document

`DELETE /document`

Request parameters (json or URL):
- index [string,required] - index of elasticsearch.
- id [integer,required] - unique identity of inserted document.

Request example:

```json
{
    "index": "foobar",
    "id": 1
}
```

Response example:

```json
{
    "status": true
}
```

### Search documents

`GET /documents`

Request parameters (json):
- index [string,required] - index of elasticsearch.
- search [string,required] - search string in title or text.
- locale [string,required] - locale of search.
- from [string,optional] - offset of returned results set. Default is 0.
- size [string,optional] - size of returned results set. Default is 50.

Request example:

```json
{
    "index": "foobar",
    "search": "lorem ipsum",
    "locale": "en"
}
```

Response parameters (json):
- id [integer] - unique identity of inserted document.
- title [string] - title of document.
- text [string] - text of document.
- locale [string] - locale of document.

Response example:

```json
{
    "status": true,
    "content": {
        "documents": [
            {
                "id": 1,
                "locale": "en",
                "title": "Hello",
                "text": "World"
            }
        ]
    }
}
```

Contributors
============

- Ilyas Makashev [mehmatovec@gmail.com](mailto:mehmatovec@gmail.com)
- Nurbek Torbayev [torbayevnurbek1992@gmail.com](mailto:torbayevnurbek1992@gmail.com)

Software
========

1. Ubuntu 16.04 Xenial
1. Nginx 1.16
1. PHP 7.4