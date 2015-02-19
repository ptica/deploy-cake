```
cake Deploy.vhost create example.com -t apache|nginx
```

Cake shell for quick creation of virtual host config file ready to be symlinked into `sites-available` directory of your webserver


* autofills current app webroot into `root/Directive`directives
* arranges log files to go into `tmp/logs` directory
* resulting virtual host configuration is put into `Config/Vhost/{template}.ctp` and the path is printed out for copy and paste
