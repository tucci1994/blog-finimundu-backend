#!/bin/sh
set -e

PORT=${PORT:-8080}
echo "Starting on port $PORT"

cat > /tmp/nginx.conf << NGINXEOF
worker_processes 1;
error_log /dev/stderr warn;
pid /tmp/nginx.pid;

events {
    worker_connections 512;
}

http {
    include /etc/nginx/mime.types;
    default_type application/octet-stream;
    client_body_temp_path /tmp;
    proxy_temp_path /tmp;
    fastcgi_temp_path /tmp;
    uwsgi_temp_path /tmp;
    scgi_temp_path /tmp;

    server {
        listen ${PORT};
        server_name _;
        root /var/www/public;
        index index.php;

        location / {
            try_files \$uri \$uri/ /index.php?\$query_string;
        }

        location ~ \\.php\$ {
            fastcgi_pass 127.0.0.1:9000;
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
        }
    }
}
NGINXEOF

echo "Starting php-fpm..."
php-fpm -D
sleep 1
echo "Starting nginx on port $PORT..."
nginx -c /tmp/nginx.conf -g "daemon off;"