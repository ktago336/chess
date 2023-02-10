
<!doctype html>
<html>
<head>
    <link href="css/button.css" rel="stylesheet" >
    <title>CHESS</title>
    <meta name="description" content="Rezi">
    <meta name = "viewport" content = "width=device-width, initial-scale=1">
</head>
<body>
<section class="section-images">
    <img src="/image.php?id={{$gameID}}&color={{$color}}&desk={{json_encode($desk)}}" align="center"  height="100" margin=0>
</section>


<style>
    html,
    body,
    .section-images {
        height: 90%;
        margin: 0;
    }
    .section-images {
        margin: auto 2em;
        text-align: center;
    }
    img {
        display: block;
        width: auto;
        height: auto;
        max-width: 100%;
        max-height: 90%;
        margin: 20px auto;
        border: 5px solid #000000;
    }
</style>




@error('error')
{{$message}}
@enderror

@if (\Illuminate\Support\Facades\Auth::check())

<form method="POST" action="/move">
    @csrf

    <input type="text" name="move">Введите ход в полной форме<br>
    <input type="submit" value="ПОХОДИТЬ" required>
</form>
<h3><a href="/giveup">GIVE UP</a></h3>
<br><br>
@endif
<h2><a href="/">HOME</a></h2>
</body>
</html>
