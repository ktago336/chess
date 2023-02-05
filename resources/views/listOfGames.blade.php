
<!doctype html>
<html>
<head>
    <link href="css/button.css" rel="stylesheet" >
    <title>CHESS</title>
    <meta name="description" content="Rezi">
    <meta name = "viewport" content = "width=device-width, initial-scale=1">
</head>
<body>
@if($errors->any())
    <h4>{{$errors->first()}}</h4>
@endif
Ваш ID: {{$id}}<br>
Начать новую игру с...
<form method="POST" action="/newgame">
    @csrf
    @error('error')
    {{ $message }}<br>
    @enderror
    <input type="text" name="name" required>

    ID игрока с которым начать игру<br>

    <input type="submit" value="Начать">
</form>
<h1>Active games</h1>
@foreach ($games as $game)
    @if (!$game->ended)
    <p>Game <a href="/game/{{$game->id}}">{{$game->id}}</a> <br>
    White ID: {{$game->white_id}}<br>
    Black ID: {{$game->black_id}}
    </p>
    @endif
@endforeach

<h2><a href="/logout">LOGOUT</a></h2>
</body>
</html>
