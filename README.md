ES HTML

Installation
============

```bash
docker run \
-p 80:80/tcp \
-e ES_HTML_HOST=example.com
-e ES_HOST=elasticsearch
-e ES_PORT=9200
```

Environment variables
=====================

- ES_HTML_HOST - server domain (without http://). Required.
- ES_HOST - Elasticsearch service url (without http://). Required.
- ES_PORT - Elasticsearch service port. Required.

Volumes
=======

If you want to make an additional configuration of container, mount your bash script to /opt/setup.sh. This script will be executed on container setup.

Software
========

1. Ubuntu 16.04 Xenial
2. PHP 7.4

How it works
============

You can store and search data by elasticsearch service

API Reference
=============