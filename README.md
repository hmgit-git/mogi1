##アプリケーション名

##環境構築
Docker Build
1. git clone git@github.com:hmgit-git/mogi1
2. docker-compose up -d --build
3. srcディレクトリにある「.env.example」をコピーして「.env」を作成し DBの設定を変更
4. phpコンテナにログイン docker-compose exec php bash
5. laravelのインストール composer install
6. アプリケーションキーを作成 php artisan key:generate
7. DBのテーブルを作成 php artisan migrate

##使用技術(実行環境)
1. PHP 8.0
2. Laravel 10.0
3. MySQL 8.0

##URL
・アプリケーション：http://localhost/ ・phpMyAdmin：http//localhost.8080/

##ER図
![ER図](ER.drawio.png)
