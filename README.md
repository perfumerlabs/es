You can store and search data by elasticsearch service

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

- ES_HOST - Elasticsearch service url (without http://). Required.
- ES_PORT - Elasticsearch service port. Default is 9200.

Volumes
=======

This image has no volumes.

If you want to make any additional configuration of container, mount your bash script to /opt/setup.sh. This script will be executed on container setup.

API Reference
=============

Software
========

1. Ubuntu 16.04 Xenial
2. PHP 7.4