Развертывание приложения на сервере apache
1) Установить apache:

2) Расположить репозиторий приложения в /var/www/

3) Установить MySQL и создать пользователя с правами администратора

4) В папке /etc/apache2/sites_available найти файл money-board.conf. В нём прописать порт, домен, alias, путь к корню приложения.

5) В файле /etc/apache2/apache2.conf прописать: 
"AccessFileName .htaccess
<Directory /var/www/>
        Options Includes Indexes FollowSymLinks
        AllowOverride All
        Require all granted
</Directory>"

6) В money-board/.env прописать в DATABASE_URL логин, пароль, порт и хост базы данных.
