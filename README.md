## TODO

Create entities :
Port
Service
Hostname
Host
Address

put php institute socket code in controller, externalize render in twig if time
put html insitute in twig template with heritage
quick test

do the clean nmap way and net_nmap way

https://blog.martinhujer.cz/symfony-forms-with-request-objects/

https://rollbar.com/guides/where-are-php-errors-logged/

Adapt willdurand nmap examples in symfony
* Check if socket config is ok
* Fix nmap scan for ip range

## Launch project

```
php bin/console server:run
yarn encore dev --watch
```

Mongo express installed with node locally. To launch it run :
```
sudo service mongodb start
cd node_modules/mongo-express/ && node app.js -u symfony -p symfony -d symfony
```
Adapt to used credentials.