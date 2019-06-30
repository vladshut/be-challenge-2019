<h1> be-challenge-2019</h1>

Docker settings located in `docker` folder
<br>
Source code is located in `src` folder
<br>
<br>
<hr>

<h3>Start project</h3>

```
cd docker
```

```
docker-compose up  --build -d nginx mysql phpmyadmin redis workspace laravel-echo-server
```

```
docker-compose exec --user=laradock  workspace composer install
```

```
docker-compose exec --user=laradock  workspace php artisan migrate
```

project api url is `http://127.0.0.1:81/api/`

project websocket url is `http://127.0.0.1:1030`

<br>
<hr>

<h3>Sockets</h3>

For start listening to the new messages in the room you can use the following code:

```
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.7.4/socket.io.js"></script>
<script>
    const socket = io('http://127.0.0.1:1030');
    socket.emit('subscribe', {
        channel: 'laravel_database_chat.<ROOM_ID>'
    }).on('App\Events\Message', function (channel, data) {
        console.log(data);
    });
</script>
```

<br>
<hr>

<h3>Run tests</h3>

```
cd docker && docker-compose exec --user=laradock  workspace composer test
```
