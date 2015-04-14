server {
        listen 80;
        server_name <?php echo preg_replace('/^www./', '', $domain) ?>;
        rewrite ^/(.*) http://<?php echo $domain ?>/$1 permanent;
}

server {
        #listen   80; ## listen for ipv4; this line is default and implied
        #listen   [::]:80 default ipv6only=on; ## listen for ipv6

        root <?php echo $webroot ?>;
        index index.php index.html index.htm;

        server_name <?php echo $domain  ?>;

        client_max_body_size 128M;
        access_log  <?php echo $logpath ?>nginx-access.log;
        error_log   <?php echo $logpath ?>nginx-error.log error;


        location / {
                # First attempt to serve request as file, then
                # as directory, then fall back to index.html
                try_files $uri $uri/ /index.php /index.html;
                if (-f $request_filename) {
                        break;
                }
                if (-d $request_filename) {
                        break;
                }
                rewrite ^(.+)$ /index.php last;
        }

        location /favicon.ico {
                # empty content
                return 204;
        }

        #error_page 404 /404.html;

        # redirect server error pages to the static page /50x.html
        #
        #error_page 500 502 503 504 /50x.html;
        #location = /50x.html {
        #       root /usr/share/nginx/www;
        #}

        # proxy the PHP scripts to Apache listening on 127.0.0.1:80
        #
        #location ~ \.php$ {
        #       proxy_pass http://127.0.0.1;
        #}

        # pass the PHP scripts to FastCGI server listening on 127.0.0.1:9000
        #
        location ~ \.php1$ {
                fastcgi_pass php;
                fastcgi_index index.php;
                include fastcgi_params;
        }

        location ~ .php$ {
                root   <?php echo $webroot ?>;
                fastcgi_pass   php;
                fastcgi_index  index.php;
                fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
                include fastcgi_params;
                fastcgi_param  QUERY_STRING     $query_string;
                fastcgi_param  REQUEST_METHOD   $request_method;
                fastcgi_param  CONTENT_TYPE     $content_type;
                fastcgi_param  CONTENT_LENGTH   $content_length;
                fastcgi_intercept_errors        on;
                fastcgi_ignore_client_abort     off;
                fastcgi_connect_timeout 60;
                fastcgi_send_timeout 180;
                fastcgi_read_timeout 180;
                fastcgi_buffer_size 128k;
                fastcgi_buffers 4 256k;
                fastcgi_busy_buffers_size 256k;
                fastcgi_temp_file_write_size 256k;
        }


        # deny access to .htaccess files, if Apache's document root
        # concurs with nginx's one
        #
        #location ~ /\.ht {
        #       deny all;
        #}
}
