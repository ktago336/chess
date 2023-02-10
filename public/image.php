<?php


use Illuminate\Support\Facades\DB;
//$gameID=$_GET['id'];
$color='w';
$cell=240;
$color=$_GET['color'];
//$color='b';
//
//$game=DB::table('games')->where('id','=',$gameID)->first();
//
$desk=json_decode($_GET['desk'],true);

$board = imagecreatefrompng('Pieces/board.png');

//$a=$desk[1][1];
//$piece=imagecreatefrompng('Pieces/'.$a.'.png');
//imagecopy($board, $piece, (0)*$cell, (5)*$cell, 0, 0, imagesx($piece), imagesy($piece));
//imagedestroy($piece);




for ($i=0;$i<=8;$i++){
    for ($j=0;$j<=8;$j++){
        if ($color=='w') {
            $trueI = $i;
            $trueJ = 9 - $j;
        }
        elseif ($color=='b'){
            $trueI = 9-$i;
            $trueJ = $j;
        }
        if (isset($desk[$trueI][$trueJ])&&$desk[$trueI][$trueJ]!='') {
            $piece=imagecreatefrompng('Pieces/'.$desk[$trueI][$trueJ].'.png');
            imagecopy($board, $piece, ($i-1)*$cell, ($j-1)*$cell, 0, 0, imagesx($piece), imagesy($piece));


        }
    }
}

header("Content-Type:image/bmp");
imagebmp($board,NULL,100);
