
<!doctype html>
<html>
<head>
    <link href="css/button.css" rel="stylesheet" >
    <title>CHESS</title>
    <meta name="description" content="Rezi">
    <meta name = "viewport" content = "width=device-width, initial-scale=1">
</head>
<body>
@error('error')
{{$message}}
@enderror
<form method="POST" action="/move">
    @csrf
    <input type="text" name="move">Введите ход в полной форме<br>
    <input type="submit" title="ПОХОДИТЬ">
</form>
<h3><a href="/">GIVE UP</a></h3>
<br><br>
<h2><a href="/">HOME</a></h2>
</body>
</html>
