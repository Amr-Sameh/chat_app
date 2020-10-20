# Chat App

### System Requirements
- PHP : 7.4
- MySQL : 14.14
- Composer : 1.10.5
- npm : 6.14.6
- redis-server : 4.0.9
- supervisor : 4.2.1

---

### Deploy Project
- create `chat_app_db` database
- copy `.env.development` or `.env.production` as `.env` depending on your environment
- you may need to change databae credentials in `.env` file
- `composer install`
- `php artisan migrate` or `php artisan project:install` this command will run migrations and seeders
- npm install
- `npm run dev` or `npm run prod` depending on your environment
- run laravel worker :
  - from command line `php artisan queue:listen` 
  - using **supervisor** [How to use Laravel Queue Worker with Supervisor](https://ekn.me/2019-11-05/how-to-use-laravel-queue-worker-with-supervisor)
- run laravel websockets :
  - from command line `php artisan websockets:serv` 
  - using **supervisor** [Starting the WebSocket server
](https://beyondco.de/docs/laravel-websockets/basic-usage/starting#keeping-the-socket-server-running-with-supervisord)

---

### Technical Aspects

- PHP Framework : Laravel 8
- JS Framework : VueJs 2.5.17
- Web Socket stack :
  - laravel-websockets free alternative to Pusher 
  - laravel-echo 
  - pusher-js
 
 