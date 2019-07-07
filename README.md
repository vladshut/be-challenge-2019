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

<h3>Bot support</h3>
In each room of the chat you can communicate with built-in bot. Just type @bot Hi! and receive reply from the bot.
<br>
Bot is built using Dialogflow service. It has some predefined intents:
<br>
<br>
<b>Welcome</b>
<br>

EN

```
> @bot hi
> Wassup bro!
```

UK

```
> @bot привіт
> драстє
```

<br>
<br>

<b>Today info</b> (using context)
<br>

EN

```
> @bot today info
> 2019-07-07 Weather, holidays?

> @bot weather 
> 10 C rain

> @bot holidays
> Easter, Day of Medicine
```


UK

```
> @bot сьогодні інфо
> 2019-07-07 Погода, свята?

> @bot погода 
> 16 С Похмуро

> @bot свята
> День молоді, 1 квітня, День бобра
```
<br>
<br>


<b>Switch language</b>
<br>

EN

```
> @bot switch lang
> Ви обрали українську мову
```

UK

```
> @bot змінити мову
> Language changed to English
```

<br>
<br>

<b>Fallback message</b>
<br>

EN

```
> @bot How many apples are on the table?
> WTF?!
```

UK

```
> @bot Який там курс?
> Що за ...?
```

<br>
<br>


