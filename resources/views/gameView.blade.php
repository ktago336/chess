
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
        margin: 0 auto;
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
    input{
        width: 50%;
        display: grid;
        justify-content: center;
        margin: 0 auto;
    }
    .inpCls{
        text-align: center;
    }
    .form{
        height: 50px;
        text-align: center;
    }
    .btn{
        width: 20%;
        height: 40px;
    }
    .home{
        position: absolute;
        right: 0px;
    }
</style>




@error('error')
{{$message}}
@enderror

@if (\Illuminate\Support\Facades\Auth::check())
<div class="inpCls">
<form method="POST" action="/move">
    @csrf

    <input class= 'form' type="text" name="move" placeholder="pd2d4"><h3>Введите ход в полной форме</h3>
    <input class= 'btn' type="submit" value="ПОХОДИТЬ" required>
</form>
</div>
<h3><a href="/giveup">GIVE UP</a>

@endif
<a class="home" href="/">HOME</a></h3>
</body>
</html>
