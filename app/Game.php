<?php

namespace App;

class Game
{
    private static function wrongMoveExit(string $msg='error'){
        return redirect()->back()->withErrors(['error' => $msg]);
    }

    public function move(&$jDesk,string $move, $state){
        $move=trim($move);
        $move=strtolower($move);

        //@TODO не забудь описать цвета фигур при превращении

        if (strlen($move)==5){  //usual move pa2a3
            if(!$this->checkPiece($jDesk,$move,$state)){
                self::wrongMoveExit();
                return 0;
            }

            $this->checkMove($jDesk, $move, $state);

        }

        elseif (strlen($move)==6){  //kill pa2xb3
            if(!$this->checkPiece($jDesk,$move,$state)){
                self::wrongMoveExit();
                return 0;
            }
            $this->checkMove($jDesk, $move, $state);
        }

        elseif (strlen($move)==2){  //00
            $this->pawnKill($jDesk,$move,$state);
        }

        elseif (strlen($move)==3){  //000

        }

        elseif (strlen($move)==7){  //pawn promotion pa7a8=q

        }

        else return 0;
        // @TODO check pnd checkmate are state properties

        //@todo AFTER ALL, before returning json !!!!!check for checks
        return $jDesk;
    }

    private function checkPiece($jDesk, string $move, $state){

        $piece=$move[0];

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
                $this->pawnKill($desk, $move, $state);
            } elseif (abs($x-$x2) == 0) {
                $this->pawnMove($desk, $move, $state);
            }
        }
        elseif ($piece=='r') {
            $this->rookMove($desk,$move,$state);
        }
        elseif ($piece=='n'){
            $this->knightMove($desk,$move,$state);
        }
        elseif ($piece=='b');
        elseif ($piece=='q');
        elseif ($piece=='k');
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
            elseif ($y2-$y1==2 && $desk[$x2][$y2]=='' && $desk[$x1+1][$y1+1]=='' && $x1==2){
                $desk[$x1][$y1] = '';
                $desk[$x2][$y2] = 'wp';
            }
            else self::wrongMoveExit();
        }
        elseif ($state['color']=='b') {
            if ($y1-$y2==1 && $desk[$x2][$y2]=='') {
                $desk[$x1][$y1] = '';
                $desk[$x2][$y2] = 'wp';
            }
            elseif ($y1-$y2==2 && $desk[$x2][$y2]=='' && $desk[$x2-1][$y2-1]=='' && $x1==7){
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
            if ($desk[$x2][$y2]=='')self::wrongMoveExit();
            if (abs($y1-$y2) == 1 && $x2-$x1==1&&(!empty($desk[$x2][$y2])&&$desk[$x2][$y2][0]=='b')){
                $desk[$x1][$y1]='';
                $desk[$x2][$y2]='wp';
            }
            else self::wrongMoveExit();

        }
        elseif ($state['color']=='b') {
            if ($desk[$x2][$y2]=='')self::wrongMoveExit();
            if (abs($y1-$y2) == 1 && $x1-$x2==1&&($desk[$x2][$y2][0]=='w')){
                $desk[$x1][$y1]='';
                $desk[$x2][$y2]='bp';
            }
            else self::wrongMoveExit();
        }
        else self::wrongMoveExit();
    }

    private function rookMove(&$desk,$move,$state){
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
            for ($i=min($y1,$y2);$i<=max($y1,$y2);$i++){
                if ($desk[$x1][$i]!=''){
                    self::wrongMoveExit();
                }
            }
            $desk[$x2][$y2]=$state['color'].'r';
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

    private function bishopMove(&$desk,$move,$state){
        //@TODO
    }
}
