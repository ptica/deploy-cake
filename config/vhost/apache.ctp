NameVirtualHost *:80

<VirtualHost *:80>
    DocumentRoot "<?php echo $webroot ?>"
    ServerName    <?php echo $domain  ?>

    ErrorLog  "<?php echo $logpath ?>apache-error.log"
    LogLevel   warn
    CustomLog "<?php echo $logpath ?>apache-access.log" combined
    #SetEnvIf Request_URI ".(ico|pdf|flv|jpg|jpeg|png|gif|js|css|gz|swf|txt)$" dontlog
    #CustomLog "<?php echo $logpath ?>apache-access.log" combined env=!dontlog

    <Directory "<?php echo $webroot ?>">
        Options MultiViews
        AllowOverride All
        Require all granted
        Options +FollowSymLinks +SymLinksIfOwnerMatch

        RewriteEngine On
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
    </Directory>
</VirtualHost>
