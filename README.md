#Swarm-Platform

##Contents
This is the repo for the "Data API" e.g. everything to do with stores and the REST API that lives on top of it. The third party docs for accessing the API are here https://docs.google.com/a/swarm-mobile.com/document/d/1CR_bRYyD02afAxqFO2DZORMb8TvunmLqp_D2nAedALg/edit?disco=AAAAAKLewao

##Installation


1. Clone the repo into your favorite folder
2. Execute /app/config/Schema/oauth.sql in your DBMS (SQL Pro, PHPMyAdmin...)
3. Create a new VirtualHost with the next structure:

**For iOS & *NIX Systems**

```
<VirtualHost *:80>
    ServerAdmin postmaster@dummy-host.localhost
    DocumentRoot "/var/www/Swarm-Platform/app/webroot"
    ServerName new.swarm-mobile.com
    ServerAlias new.swarm-mobile.com newapi.swarm-mobile.com
    ErrorLog "logs/new.swarm-mobile.com-error.log"
    CustomLog "logs/new.swarm-mobile.com-access.log" combined
    <Directory />
        Options FollowSymLinks
        AllowOverride All
    </Directory>
</VirtualHost>
```

**For Windows**

```
<VirtualHost *:80>
    ServerAdmin postmaster@dummy-host.localhost
    DocumentRoot "C:/xampp/htdocs/Swarm-Platform/app/webroot"
    ServerName new.swarm-mobile.com
    ServerAlias new.swarm-mobile.com newapi.swarm-mobile.com
    ErrorLog "logs/new.swarm-mobile.com-error.log"
    CustomLog "logs/new.swarm-mobile.com-access.log" combined
    <Directory />
        Options FollowSymLinks
        AllowOverride All
    </Directory>
</VirtualHost>
```

**NOTES:** *new.swarm-mobile.com & newapi.swarm-mobile.com can be whatever you want 
and also you can modify your DocumentRoot path.*

##Test the API

At some moments it's possible that you need to test some specific method via URL and you don't 
like to generate an access_token. For these cases, enable the debug of the API via
setting the $debug var to true in /app/Controller/APIController.php
