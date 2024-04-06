
# FreeForAll

OpenCart payment plugin for giving products away for free and sending order data to a manufacturing execution system (MES).

## Edit inside Docker image

* Find storage directory für image
* Open file under /opt/bitnami/opencart/extension/opencart/catalog/controller/payment

## Show error log messages from Docker container

* Change to directory: /var/lib/docker/volumes/opencart_opencart_storage_data/_data
* Execute command: tail -f logs/error.log 
