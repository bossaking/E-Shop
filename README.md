1. Pobrać [PHP 7.3 (7.3.29) VC15 x64 Thread Safe (ZIP)](https://windows.php.net/download#php-7.3) 
2. Utworzyć nowy folder C:\php i rozpakować do tego folderu pobrany archiwum z PHP
3. Dodać C:\php do PATH
4. W pliku php.ini usunąć średnik przed **_extension=pdo_mysql_**
5. Pobrać i zainstalować [MySQL Connector (MSI Installer)](https://dev.mysql.com/downloads/connector/odbc/)
6. W procesie konfiguracji serwera MySQL utworzyć użytkownika o nazwie **_'root'_** z hasłem **_'root'_**
7. Do pliku **_my.ini_** dodać:

**[mysqld]**<br>
**default_authentication_plugin= mysql_native_password**

8. Za pomocą MySQL Workbench połączyć się z bazą danych, utworzyć i uruchomić zapytania:

**ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'root';<br>
ALTER USER 'root'@'%' IDENTIFIED WITH mysql_native_password BY 'root';<br>
ALTER USER 'default'@'%' IDENTIFIED WITH mysql_native_password BY 'secret';<br>**

9. Pobrać i zainstalować [COMPOSER](https://getcomposer.org/download/)
10. Pobrać i zainstalować [SYMFONY CLI](https://symfony.com/download)

### Kolejne kroki należy wykonywać w Windows Power Shell

11. composer install
12. symfony console doctrine:database:create
13. symfony console doctrine:migrations:migrate
14. symfony serve
15. Strona będzie dostępna pod **_localhost:8000_**


### W przypadku problemów:

1. 404 przy odpalaniu - php bin/console cache:clear --env=prod
2. Nie widać zmian CSS - CTRL + F5
3. Nie zatrzymuje się lokalny serwer - symfony server:stop
