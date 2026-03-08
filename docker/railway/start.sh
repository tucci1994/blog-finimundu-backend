#!/bin/sh
set -e

PORT=${PORT:-8080}
echo "Starting on port $PORT"

cat > /tmp/nginx.conf << NGINXEOF
worker_processes auto;
error_log /dev/stderr warn;
pid /tmp/nginx.pid;

events {
    worker_connections 1024;
}

http {
    include /etc/nginx/mime.types;
    default_type application/octet-stream;
    sendfile on;
    keepalive_timeout 65;
    client_body_temp_path /tmp/client_body;
    fastcgi_temp_path /tmp/fastcgi;

    server {
        listen ${PORT};
        server_name _;
        root /var/www/public;
        index index.php;
        charset utf-8;

        location / {
            try_files \$uri \$uri/ /index.php?\$query_string;
        }

        location ~ \\.php\$ {
            fastcgi_pass 127.0.0.1:9000;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
            fastcgi_param QUERY_STRING \$query_string;
            fastcgi_param REQUEST_METHOD \$request_method;
            fastcgi_param CONTENT_TYPE \$content_type;
            fastcgi_param CONTENT_LENGTH \$content_length;
            fastcgi_param SCRIPT_NAME \$fastcgi_script_name;
            fastcgi_param REQUEST_URI \$request_uri;
            fastcgi_param DOCUMENT_URI \$document_uri;
            fastcgi_param DOCUMENT_ROOT \$document_root;
            fastcgi_param SERVER_PROTOCOL \$server_protocol;
            fastcgi_param GATEWAY_INTERFACE CGI/1.1;
            fastcgi_param SERVER_SOFTWARE nginx;
            fastcgi_param REMOTE_ADDR \$remote_addr;
            fastcgi_param REMOTE_PORT \$remote_port;
            fastcgi_param SERVER_ADDR \$server_addr;
            fastcgi_param SERVER_PORT \$server_port;
            fastcgi_param SERVER_NAME \$server_name;
            fastcgi_param HTTPS \$https if_not_empty;
        }

        location ~ /\\.(?!well-known).* {
            deny all;
        }
    }
}
NGINXEOF

php-fpm -D
nginx -g "daemon off;" -c /tmp/nginx.conf
