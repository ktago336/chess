<?php

namespace App;

class Game
{
    private static function wrongMoveExit(string $msg='error'){
        return redirect()->back()->withErrors(['error' => $msg]);
    }

    public function move(&$jDesk,string $move, &$state){
        $originalDesk=$jDesk;

        $move=trim($move);
        $move=trim($move,'+');
        $move=strtolower($move);
        $buf=$this->convertKill($move);
        if ($move[0]=='p'&& (intval($buf[2])==8||intval($buf[2])==1)){
            return redirect()->back()->withErrors("error","ERROR promotion");
        }

        if (!$this->validateMove($move)){
            self::wrongMoveExit('move not valid');
            return 0;
        }
        //@TODO добавь в validateMove проверку рокировок

        if (strlen($move)==5){  //usual move pa2a3
            if(!$this->checkPiece($jDesk,$move,$state)){
                self::wrongMoveExit();
                return 0;
            }

            $this->checkMove($jDesk, $move, $state);

        }

        elseif (strlen($move)==6){  //kill pa2xb3
            if(!$this->checkPiece($jDesk,$move,$state)||!$this->checkKill($jDesk,$move,$state)){
                self::wrongMoveExit();
                return 0;
            }
            $move=$this->convertKill($move);
            $this->checkMove($jDesk,$move,$state);
            return $jDesk;
        }

        elseif (strlen($move)==2){  //00
            $this->castling($jDesk, $move, $state);
        }

        elseif (strlen($move)==3){  //000
            $this->castling($jDesk, $move, $state);

        }

        elseif (strlen($move)==7){  //pawn promotion pa7a8=q
            if(!$this->checkPiece($jDesk,$move,$state)||!$this->checkKill($jDesk,$move,$state)){
                self::wrongMoveExit();
                return 0;
            }
            $move=$this->convertKill($move);
            $this->checkMove($jDesk,$move,$state);
        }
        elseif (strlen($move)==8){  // pa7xb8=q
            if(!$this->checkPiece($jDesk,$move,$state)||!$this->checkKill($jDesk,$move,$state)){
                self::wrongMoveExit();
                return 0;
            }
            $move=$this->convertKill($move);
            $this->checkMove($jDesk,$move,$state);
        }

        else return redirect()->back()->withErrors("error",'error');

        if ($this->checked($jDesk)==$state['color']){
            $jDesk=$originalDesk;
            return redirect()->back()->withErrors("error",'checked');
        }
        if ($this->checked($jDesk)!=0&&$this->checked($jDesk)!=$state['color']){
            if ($state['color']=='w') $state['black_checked']=1;
            if ($state['color']=='b') $state['white_checked']=1;
        }
        if ($this->checked($jDesk)==0){
            $state['black_checked']=0;
            $state['white_checked']=0;
        }

        else return $jDesk;
    }

    private function checkKill($desk,$move,$state){
        $move=$this->convertKill($move);
        $x1=$this->aton($move[1]);
        $y1=intval($move[2]);
        $x2=$this->aton($move[3]);
        $y2=intval($move[4]);

        if (empty($desk[$x2][$y2])){
            self::wrongMoveExit('wrong kill move');
            return false;
        }
        elseif ($desk[$x2][$y2][0]==$state['color']){
            self::wrongMoveExit('wrong kill move');
            return false;
        }
        else return true;
    }

    private function convertKill($move){
        return str_replace('x','',$move);
    }

    private function checkPiece($jDesk, string $move, $state):bool{

        $piece=$move[0];
        $move=$this->convertKill($move);
        $x=$this->aton($move[1]);
        $y=intval($move[2]);
        $x2=$this->aton($move[3]);
        $y2=intval($move[4]);

        if ($jDesk[$x2][$y2]!='' && $jDesk[$x2][$y2][0]==$state['color'])
            self::wrongMoveExit();

        if ($x==$x2&&$y==$y2)
            self::wrongMoveExit();

        if ($x2<1||$x2>8||$y2<1||$y2>8){
            self::wrongMoveExit();
        }

        if (isset($jDesk[$x][$y][0]) && $jDesk[$x][$y][0]==$state['color'] && $jDesk[$x][$y][1]==$piece) return true;
        else {
            return false;
        }

    }

    private function validateMove($move){
        $pieces=array('p','r','n','b','q','k');
        $letters=array('a','b','c','d','e','f','g','h');

        $piece=$move[0];
        //$x=$this->aton($move[1]);
        //$y=intval($move[2]);
        //$x2=$this->aton($move[3]);
        $y2=intval($move[4]);
        if ($piece=='p' && ($y2==1||$y2==8) && strlen($move)!=7 ){
            self::wrongMoveExit();
        }

        if (strlen($move)==6&&$move[3]=='x'){  //kill pa2xb3
            $move=str_replace('x','',$move);
        }
        if (strlen($move)==6&&$move[3]!='x'){  //kill pa2xb3
            return 0;
        }

        if (strlen($move)==5){  //usual move pa2a3
               if(!in_array($move[0],$pieces)){
                   return 0;
               }
               elseif (!in_array($move[1],$letters)){
                   return 0;
               }
               elseif (!in_array($move[3],$letters)){
                   return 0;
               }
               elseif (intval($move[2])>8||intval($move[2])<1){
                   return 0;
               }
               elseif (intval($move[4])>8||intval($move[4])<1){
                   return 0;
               }
               else return 1;

        }

        elseif (strlen($move)==2){  //00
            if ($move[0]!='0'||$move[1]!='0'){
                return 0;
            }
            else return 1;

        }

        elseif (strlen($move)==3){  //000
            if ($move[0]!='0'||$move[1]!='0'||$move[2]!='0'){
                return 0;
            }
            else return 1;

        }

        elseif (strlen($move)==7){  //pawn promotion pa7a8=q
            if(!in_array($move[0],$pieces)){
                return 0;
            }
            elseif (!in_array($move[1],$letters)){
                return 0;
            }
            elseif (!in_array($move[3],$letters)){
                return 0;
            }
            elseif (intval($move[2])>8||intval($move[2])<1){
                return 0;
            }
            elseif (intval($move[4])>8||intval($move[4])<1){
                return 0;
            }
            elseif ($move[5]!='='){
                return 0;
            }
            elseif (!in_array($move[6],$letters)){
                return 0;
            }
            elseif (strlen($move)==8){  //pawn promotion pa7xb8=q
                if(!in_array($move[0],$pieces)){
                    return 0;
                }
                elseif (!in_array($move[1],$letters)){
                    return 0;
                }
                elseif (!in_array($move[4],$letters)){
                    return 0;
                }
                elseif (intval($move[2])>8||intval($move[2])<1){
                    return 0;
                }
                elseif (intval($move[5])>8||intval($move[5])<1){
                    return 0;
                }
                elseif ($move[6]!='='){
                    return 0;
                }
                elseif (!in_array($move[7],$letters)){
                    return 0;
                }
                else return 1;
            }
            else return 1;
        }
        else return 0;
    }

    private function aton($a): int
    {
        $table=array('a'=>1,
            'b'=>2,
            'c'=>3,
            'd'=>4,
            'e'=>5,
            'f'=>6,
            'g'=>7,
            'h'=>8,);
        return $table[$a];
    }

    private function checkMove(&$desk, $move, $state){
        $piece=$move[0];
        $x=$this->aton($move[1]);
        $y=intval($move[2]);
        $x2=$this->aton($move[3]);
        $y2=intval($move[4]);


        if ($piece=='p') {
            if (abs($x-$x2)==1) {
                if ($y2!=8||$y2!=1)
                    $this->pawnKill($desk, $move, $state);
                if ($y2==8||$y2==1)
                    $this->pawnPromotionKill($desk,$move,$state);
            }
            elseif (abs($x-$x2) == 0) {
                if ($y2!=8||$y2!=1)
                    $this->pawnMove($desk, $move, $state);
                if ($y2==8||$y2==1)
                    $this->pawnPromotion($desk,$move,$state);
            }
        }
        elseif ($piece=='r') {
            $this->rookMove($desk,$move,$state);
        }
        elseif ($piece=='n'){
            $this->knightMove($desk,$move,$state);
        }
        elseif ($piece=='b'){
            $this->bishopMove($desk,$move,$state);
        }
        elseif ($piece=='q'){
            $this->queenMove($desk,$move,$state);
        }
        elseif ($piece=='k'){
            $this->kingMove($desk,$move,$state);
        }
        else self::wrongMoveExit();
    }

    private function pawnMove(&$desk,$move,$state){

        $x1=$this->aton($move[1]);
        $y1=intval($move[2]);
        $x2=$this->aton($move[3]);
        $y2=intval($move[4]);

        if ($x1!=$x2) self::wrongMoveExit();

        if ($state['color']=='w') {
            if ($y2-$y1==1 && $desk[$x2][$y2]=='') {
                $desk[$x1][$y1] = '';
                $desk[$x2][$y2] = 'wp';
            }
            elseif ($y2-$y1==2 && $desk[$x2][$y2]=='' && $desk[$x1][$y1+1]=='' && $y1==2){
                $desk[$x1][$y1] = '';
                $desk[$x2][$y2] = 'wp';
            }
            else self::wrongMoveExit();
        }
        elseif ($state['color']=='b') {
            if ($y1-$y2==1 && $desk[$x2][$y2]=='') {
                $desk[$x1][$y1] = '';
                $desk[$x2][$y2] = 'bp';
            }
            elseif ($y1-$y2==2 && $desk[$x2][$y2]=='' && $desk[$x2][$y2-1]=='' && $y1==7){
                $desk[$x1][$y1] = '';
                $desk[$x2][$y2] = 'bp';
            }
            else self::wrongMoveExit();
        }
    }

    private function pawnKill(&$desk,$move,$state){

        $x1=$this->aton($move[1]);
        $y1=intval($move[2]);
        $x2=$this->aton($move[3]);
        $y2=intval($move[4]);

        if ($state['color']=='w') {
            if (abs($y1-$y2) == 1 && $x2-$x1==1&&(!empty($desk[$x2][$y2])&&$desk[$x2][$y2][0]=='b')){
                $desk[$x1][$y1]='';
                $desk[$x2][$y2]='wp';
            }
            else self::wrongMoveExit();

        }
        elseif ($state['color']=='b') {
            if (abs($y1-$y2) == 1 && $x1-$x2==1&&($desk[$x2][$y2][0]=='w')){
                $desk[$x1][$y1]='';
                $desk[$x2][$y2]='bp';
            }
            else self::wrongMoveExit();
        }
        else self::wrongMoveExit();
    }

    private function rookMove(&$desk,$move,$state,$piece='r'){
        $x1=$this->aton($move[1]);
        $y1=intval($move[2]);
        $x2=$this->aton($move[3]);
        $y2=intval($move[4]);

        if (($x1!=$x2)&&($y1!=$y2)) self::wrongMoveExit();


        $buf=array();
        $buf=$desk;
        $buf[$x1][$y1]='';
        $buf[$x2][$y2]='';
        if ($x1==$x2){
            for ($i=min($y1,$y2)+1;$i<max($y1,$y2);$i++){
                if ($desk[$x1][$i]!=''){
                    self::wrongMoveExit('rook error');
                }
            }
            $desk[$x2][$y2]=$state['color'].'r';
            $desk[$x1][$y1]='';
        }
        if ($y1==$y2){
            for ($i=min($x1,$x2)+1;$i<max($x1,$x2);$i++){
                if ($desk[$y1][$i]!=''){
                    self::wrongMoveExit('rook error');
                }
            }
            $desk[$x2][$y2]=$state['color'].$piece;
            $desk[$x1][$y1]='';
        }
    }

    private function knightMove(&$desk,$move,$state){
        $x1=$this->aton($move[1]);
        $y1=intval($move[2]);
        $x2=$this->aton($move[3]);
        $y2=intval($move[4]);

        if ((abs($x1-$x2)==2 && abs($y1-$y2)==1)||(abs($x1-$x2)==1 && abs($y1-$y2)==2)){
            $desk[$x2][$y2]=$state['color'].'n';
            $desk[$x1][$y1]='';
        }
        else self::wrongMoveExit();
    }

    private function bishopMove(&$desk,$move,$state, $piece='b'){
        $x1=$this->aton($move[1]);
        $y1=intval($move[2]);
        $x2=$this->aton($move[3]);
        $y2=intval($move[4]);
        if (abs($x1-$x2)!=abs($y1-$y2)){
            self::wrongMoveExit();
        }
        for ($i=min($x1,$x2)+1; $i<max($x1,$x2); $i++){
            for ($j=min($y1,$y2)+1; $j<max($y1,$y2); $j++){
                if($desk[$i][$j]!=''){
                    self::wrongMoveExit('bishop error');
                }
            }
        }
        $desk[$x1][$y1]='';
        $desk[$x2][$y2]=$state['color'].$piece;
    }

    private function queenMove(&$desk,$move,$state){
        $x1=$this->aton($move[1]);
        $y1=intval($move[2]);
        $x2=$this->aton($move[3]);
        $y2=intval($move[4]);

        if (($x1!=$x2)&&($y1!=$y2)&&(abs($x1-$x2)!=abs($y1-$y2))){
            self::wrongMoveExit('queen move error');
        }

        if ($x1==$x2||$y1==$y2){
            $this->rookMove($desk,$move,$state,'q');
        }
        elseif (abs($x1-$x2)==abs($y1-$y2)){
            $this->bishopMove($desk,$move,$state,'q');
        }
        else self::wrongMoveExit();
    }

    private function kingMove(&$desk,$move,$state){
        $x1=$this->aton($move[1]);
        $y1=intval($move[2]);
        $x2=$this->aton($move[3]);
        $y2=intval($move[4]);

        if (abs($x1-$x2)>1||abs($y1-$y2)>1){
            self::wrongMoveExit('wrong king move');
        }

        $desk[$x1][$y1]='';
        $desk[$x2][$y2]=$state['color'].'k';
    }

    private function pawnPromotion(&$desk,$move,$state){
        //self::wrongMoveExit('pawn promotion started');
       // return redirect()->back()->withErrors('error','ERRROR');
       // echo "<script>console.log('pawnProm started' );</script>";
        $x1=$this->aton($move[1]);
        $y1=intval($move[2]);
        $x2=$this->aton($move[3]);
        $y2=intval($move[4]);
        if(strlen($move)!=7){   //pa7a8=q
            self::wrongMoveExit('wrong move');
            //return 0;
        }

        if ($move[6]=='p'){
            self::wrongMoveExit('pawn error');
        }
        if ($x1!=$x2) self::wrongMoveExit();

        if ($state['color']=='w') {
            if ($y2-$y1==1 && $desk[$x2][$y2]==''&& $y2==8) {
                $desk[$x1][$y1] = '';
                $desk[$x2][$y2] = 'w'.$move[6];
            }
            else self::wrongMoveExit();

        }
        elseif ($state['color']=='b') {
            if ($y1-$y2==1 && $desk[$x2][$y2]=='' && $y2==1) {
                $desk[$x1][$y1] = '';
                $desk[$x2][$y2] = 'w'.$move[6];
            }
            else self::wrongMoveExit();
        }
    }

    private function pawnPromotionKill(&$desk,$move,$state){
        //self::wrongMoveExit('pawn promotion kill started');
        //return redirect()->back()->withErrors('error','ERRROR');

        //echo "<script>console.log('pawnPromKill started' );</script>";

        $x1=$this->aton($move[1]);
        $y1=intval($move[2]);
        $x2=$this->aton($move[3]);
        $y2=intval($move[4]);

        if(strlen($move)!=7){   //pa7xa8=q
            self::wrongMoveExit('wrong move');
            //return 0;
        }

        if ($move[6]=='p'){
            self::wrongMoveExit('pawn error');
        }
        if (abs($x1-$x2)!=1) self::wrongMoveExit();

        if ($state['color']=='w') {
            if ($y2-$y1==1 && $desk[$x2][$y2][0]=='b'&& $y2==8) {
                $desk[$x1][$y1] = '';
                $desk[$x2][$y2] = 'w'.$move[6];
            }
            else self::wrongMoveExit();
        }
        elseif ($state['color']=='b') {
            if ($y1-$y2==1 && $desk[$x2][$y2][0]=='b' && $y2==1) {
                $desk[$x1][$y1] = '';
                $desk[$x2][$y2] = 'w'.$move[6];
            }
            else self::wrongMoveExit();
        }
    }

    private function castling (&$desk,$move,&$state)
    {
        $gameID = $state['game_id'];


        if ($move == '000') {
            if ($state['color'] == 'w' && $state['white_can_000'] == 1) {
                if ($desk[2][1] == '' && $desk[3][1] == '' && $desk[4][1] == '') {
                    $desk[4][1] = 'wr';
                    $desk[3][1] = 'wk';
                    $desk[1][1] = '';
                    $desk[5][1] = '';
                    $state['white_can_000']=0;
                    $state['white_can_00']=0;
                }
            }
            elseif ($state['color'] == 'b' && $state['black_can_000'] == 1) {
                if ($desk[2][8] == '' && $desk[3][8] == '' && $desk[4][8] == '') {
                    $desk[4][8] = 'wr';
                    $desk[3][8] = 'wk';
                    $desk[1][8] = '';
                    $desk[5][8] = '';
                    $state['black_can_00']=0;
                    $state['black_can_00']=0;

                }
            }
            else redirect()->back()->withErrors('error');
        }

        elseif ($move == '00') {
                if ($state['color'] == 'w' && $state['white_can_00']==1) {
                    if ($desk[6][1] == '' && $desk[7][1] == '') {
                        $desk[6][1] = 'wr';
                        $desk[7][1] = 'wk';
                        $desk[5][1] = '';
                        $desk[8][1] = '';
                        $state['white_can_000']=0;
                        $state['white_can_00']=0;
                    }
                } elseif ($state['color'] == 'b' && $state['black_can_00']==1) {
                    if ($desk[6][8] == '' && $desk[7][8] == '') {
                        $desk[6][8] = 'wr';
                        $desk[7][8] = 'wk';
                        $desk[5][8] = '';
                        $desk[8][8] = '';
                        $state['black_can_00']=0;
                        $state['black_can_00']=0;
                    }
                }
                else redirect()->back()->withErrors('error');
        }
        else return redirect()->back()->withErrors('error');
    }

    public function checked($desk){
        for ($i=1;$i<=8;$i++){
            for ($j=1;$j<=8;$j++){
                if (!empty($desk[$i][$j]&&$desk[$i][$j]=='wk')){
                    //
                    //white king found
                    //

                    if ((isset($desk[$i+1][$j+1])&&$desk[$i+1][$j+1]=='bk')||
                        (isset($desk[$i][$j+1])&&$desk[$i+1][$j+1]=='bk')||
                        (isset($desk[$i-1][$j+1])&&$desk[$i+1][$j+1]=='bk')||
                        (isset($desk[$i+1][$j])&&$desk[$i+1][$j+1]=='bk')||
                        (isset($desk[$i-1][$j])&&$desk[$i+1][$j+1]=='bk')||
                        (isset($desk[$i+1][$j-1])&&$desk[$i+1][$j+1]=='bk')||
                        (isset($desk[$i][$j-1])&&$desk[$i+1][$j+1]=='bk')||
                        (isset($desk[$i-1][$j-1])&&$desk[$i+1][$j+1]=='bk')
                    ){
                        return 'w';
                    }

                    if((isset($desk[$i+1][$j+1])&&$desk[$i+1][$j+1]=='bp')||
                        (isset($desk[$i-1][$j+1])&&$desk[$i-1][$j+1]=='bp')){
                        return 'w';
                    }
                    if (isset($desk[$i+1][$j+2]) && !empty($desk[$i+1][$j+2]) &&  $desk[$i+1][$j+2]=='bn'||
                        isset($desk[$i+2][$j+1]) && !empty($desk[$i+2][$j+1]) &&  $desk[$i+2][$j+1]=='bn'||
                        isset($desk[$i+1][$j-2]) && !empty($desk[$i+1][$j-2]) &&  $desk[$i+1][$j-2]=='bn'||
                        isset($desk[$i+2][$j-1]) && !empty($desk[$i+2][$j-1]) &&  $desk[$i+2][$j-1]=='bn'||
                        isset($desk[$i-1][$j+2]) && !empty($desk[$i-1][$j+2]) &&  $desk[$i-1][$j+2]=='bn'||
                        isset($desk[$i-2][$j+1]) && !empty($desk[$i-2][$j+1]) &&  $desk[$i-2][$j+1]=='bn'||
                        isset($desk[$i-1][$j-2]) && !empty($desk[$i-1][$j-2]) &&  $desk[$i-1][$j-2]=='bn'||
                        isset($desk[$i-2][$j-1]) && !empty($desk[$i-2][$j-1]) &&  $desk[$i-2][$j-1]=='bn'
                    ){
                        return 'w';
                    }

                    $xUpClear=false;
                    $xDownClear=false;
                    $yUpClear=false;
                    $yDownClear=false;

                    $xUp_yUpClear=false;
                    $xDown_yUpClear=false;
                    $xUp_yDownClear=false;
                    $xDown_yDownClear=false;


                    for ($col=1; $col<=8;$col++){
                        //
                        //rook + queen check
                        //
                        if($desk[$i+$col][$j]!=''&&!$xUpClear) {
                            if ($desk[$i + $col][$j] == 'br' || $desk[$i + $col][$j] == 'bq') {
                                return 'w';
                            } else $xUpClear = true;
                        }
                        if($desk[$i-$col][$j]!=''&&!$xDownClear){
                            if($desk[$i-$col][$j]=='br'||$desk[$i-$col][$j]=='bq'){
                                return 'w';
                            }
                            else $xDownClear=true;
                        }
                        if($desk[$i][$j+$col]!=''&&!$yUpClear){
                            if($desk[$i][$j+$col]=='br'||$desk[$i][$j+$col]=='bq'){
                                return 'w';
                            }
                            else $yUpClear=true;
                        }
                        if($desk[$i][$j-$col]!=''&&!$yDownClear){
                            if($desk[$i][$j-$col]=='br'||$desk[$i][$j-$col]=='bq'){
                                return 'w';
                            }
                            else $yDownClear=true;
                        }
                        //
                        //bishop and queen check
                        //
                        if($desk[$i+$col][$j+$col]!=''&&!$xUp_yUpClear){
                            if($desk[$i+$col][$j+$col]=='bb'||$desk[$i+$col][$j+$col]=='bq'){
                                return 'w';
                            }
                            else $xUp_yUpClear=true;
                        }
                        if($desk[$i+$col][$j-$col]!=''&&!$xUp_yDownClear){
                            if($desk[$i+$col][$j-$col]=='bb'||$desk[$i+$col][$j-$col]=='bq'){
                                return 'w';
                            }
                            else $xUp_yUpClear=true;
                        }
                        if($desk[$i-$col][$j+$col]!=''&&!$xDown_yUpClear){
                            if($desk[$i-$col][$j+$col]=='bb'||$desk[$i-$col][$j+$col]=='bq'){
                                return 'w';
                            }
                            else $xUp_yUpClear=true;
                        }
                        if($desk[$i-$col][$j-$col]!=''&&!$xDown_yDownClear){
                            if($desk[$i-$col][$j-$col]=='bb'||$desk[$i-$col][$j-$col]=='bq'){
                                return 'w';
                            }
                            else $xUp_yUpClear=true;
                        }
                    }
                    return 0;
                }


                elseif (!empty($desk[$i][$j]&&$desk[$i][$j]=='bk')){
                    //
                    //black king found
                    //

                    if ((isset($desk[$i+1][$j+1])&&$desk[$i+1][$j+1]=='wk')||
                        (isset($desk[$i][$j+1])&&$desk[$i+1][$j+1]=='wk')||
                        (isset($desk[$i-1][$j+1])&&$desk[$i+1][$j+1]=='wk')||
                        (isset($desk[$i+1][$j])&&$desk[$i+1][$j+1]=='wk')||
                        (isset($desk[$i-1][$j])&&$desk[$i+1][$j+1]=='wk')||
                        (isset($desk[$i+1][$j-1])&&$desk[$i+1][$j+1]=='wk')||
                        (isset($desk[$i][$j-1])&&$desk[$i+1][$j+1]=='wk')||
                        (isset($desk[$i-1][$j-1])&&$desk[$i+1][$j+1]=='wk')
                    ){
                        return 'b';
                    }

                    if((isset($desk[$i+1][$j-1])&&$desk[$i+1][$j-1]=='wp')||
                        (isset($desk[$i-1][$j-1])&&$desk[$i-1][$j-1]=='wp')){
                        return 'b';
                    }
                    if (isset($desk[$i+1][$j+2]) && !empty($desk[$i+1][$j+2]) &&  $desk[$i+1][$j+2]=='wn'||
                        isset($desk[$i+2][$j+1]) && !empty($desk[$i+2][$j+1]) &&  $desk[$i+2][$j+1]=='wn'||
                        isset($desk[$i+1][$j-2]) && !empty($desk[$i+1][$j-2]) &&  $desk[$i+1][$j-2]=='wn'||
                        isset($desk[$i+2][$j-1]) && !empty($desk[$i+2][$j-1]) &&  $desk[$i+2][$j-1]=='wn'||
                        isset($desk[$i-1][$j+2]) && !empty($desk[$i-1][$j+2]) &&  $desk[$i-1][$j+2]=='wn'||
                        isset($desk[$i-2][$j+1]) && !empty($desk[$i-2][$j+1]) &&  $desk[$i-2][$j+1]=='wn'||
                        isset($desk[$i-1][$j-2]) && !empty($desk[$i-1][$j-2]) &&  $desk[$i-1][$j-2]=='wn'||
                        isset($desk[$i-2][$j-1]) && !empty($desk[$i-2][$j-1]) &&  $desk[$i-2][$j-1]=='wn'
                    ){
                        return 'b';
                    }

                    $xUpClear=false;
                    $xDownClear=false;
                    $yUpClear=false;
                    $yDownClear=false;

                    $xUp_yUpClear=false;
                    $xDown_yUpClear=false;
                    $xUp_yDownClear=false;
                    $xDown_yDownClear=false;

                    for ($col=1; $col<=8;$col++){
                        //
                        //rook + queen check
                        //
                        if($desk[$i+$col][$j]!=''&&!$xUpClear){
                            if($desk[$i+$col][$j]=='wr'||$desk[$i+$col][$j]=='wq'){
                                return 'b';
                            }
                            else $xUpClear=true;
                        }
                        if($desk[$i-$col][$j]!=''&&!$xDownClear){
                            if($desk[$i-$col][$j]=='wr'||$desk[$i-$col][$j]=='wq'){
                                return 'b';
                            }
                            else $xDownClear=true;
                        }
                        if($desk[$i][$j+$col]!=''&&!$yUpClear){
                            if($desk[$i][$j+$col]=='wr'||$desk[$i][$j+$col]=='wq'){
                                return 'b';
                            }
                            else $yUpClear=true;
                        }
                        if($desk[$i][$j-$col]!=''&&!$yDownClear){
                            if($desk[$i][$j-$col]=='wr'||$desk[$i][$j-$col]=='wq'){
                                return 'b';
                            }
                            else $yDownClear=true;
                        }
                        //
                        //bishop and queen check
                        //
                        if($desk[$i+$col][$j+$col]!=''&&!$xUp_yUpClear){
                            if($desk[$i+$col][$j+$col]=='wb'||$desk[$i+$col][$j+$col]=='wq'){
                                return 'b';
                            }
                            else $xUp_yUpClear=true;
                        }
                        if($desk[$i+$col][$j-$col]!=''&&!$xUp_yDownClear){
                            if($desk[$i+$col][$j-$col]=='wb'||$desk[$i+$col][$j-$col]=='wq'){
                                return 'b';
                            }
                            else $xUp_yUpClear=true;
                        }
                        if($desk[$i-$col][$j+$col]!=''&&!$xDown_yUpClear){
                            if($desk[$i-$col][$j+$col]=='wb'||$desk[$i-$col][$j+$col]=='wq'){
                                return 'b';
                            }
                            else $xUp_yUpClear=true;
                        }
                        if($desk[$i-$col][$j-$col]!=''&&!$xDown_yDownClear){
                            if($desk[$i-$col][$j-$col]=='wb'||$desk[$i-$col][$j-$col]=='wq'){
                                return 'b';
                            }
                            else $xUp_yUpClear=true;
                        }
                    }
                    return 0;

                }
            }
        }
    }

}
