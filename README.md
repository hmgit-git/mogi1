## アプリケーション名

## 環境構築
Docker Build
1. git clone git@github.com:hmgit-git/mogi1
2. docker-compose up -d --build
3. srcディレクトリにある「.env.example」をコピーして「.env」を作成し DBの設定を変更
```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```
4. phpコンテナにログイン docker-compose exec php bash
5. laravelのインストール composer install
6. アプリケーションキーを作成 php artisan key:generate
7. DBのテーブルを作成 php artisan migrate
8. ダミーデータの登録 php artisan db:seed
9. mailhogのインストール、.envに下記のように記載
```
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=test@example.com
MAIL_FROM_NAME="coachtechフリマ"
```
10. シンボリックリンクの作成 php artisan storage:link

11. "The stream or file could not be opened"エラーが発生した場合
srcディレクトリにあるstorageディレクトリに権限を設定
chmod -R 777 storage

12. テスト用DBの作成
```
docker-compose exec mysql bash
mysql -u root -p
CREATE DATABASE demo_test;
```
13. テスト用設定ファイルを作成
cp .env .env.testing

14. テスト用設定ファイルを変更
```
APP_ENV=test
APP_KEY=
DB_CONNECTION=mysql_test
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=demo_test
DB_USERNAME=root
DB_PASSWORD=root
```
15. テスト用のAPP_KEYの作成とキャッシュクリア
```
docker-compose exec php bash
php artisan key:generate --env=testing
php artisan config:clear
```
16. テスト用DBのマイグレーション
php artisan migrate --env=testing

17. テストの実行
php artisan test

18. サンプルユーザでログインする場合は下記をご使用ください
```
- サンプルユーザ１
ユーザ名：user1@example.com
パスワード：password
- サンプルユーザ２
ユーザ名：user2@example.com
パスワード：password
- サンプルユーザ３
ユーザ名：user3@example.com
パスワード：password
```

## 使用技術(実行環境)
1. PHP 7.4.9
2. Laravel 8.83.8
3. MySQL 15.1
4. nginx 1.21.1

## URL
・アプリケーション：http://localhost/ ・phpMyAdmin：http//localhost.8080/　・mailhog：http://localhost:8025/

## ER図

![ER図](ER.drawio.png)
