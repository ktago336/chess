<?php

namespace App;

class Game
{

    //@ $state->color;
    public function move($jDesk,string $move, $state){
        $move=trim($move);
        $move=strtolower($move);


        //@TODO не забудь описать цвета фигур при превращении

        if (strlen($move)==5){  //usual move pa2a3
            if(!$this->checkPiece($jDesk,$move,$state)){
                self::wrongMoveExit();
                return 0;
            }
            //@todo checkmove
        }

        elseif (strlen($move)==6){  //kill pa2xb3

        }

        elseif (strlen($move)==2){  //00

        }

        elseif (strlen($move)==3){  //000

        }

        elseif (strlen($move)==7){  //pawn promotion pa7a8=q

        }

        else return 0;
        // @TODO check pnd checkmate are state properties
        return $jDesk;
    }

    private function checkPiece($jDesk, string $move, $state){
        $piece=$move[0];

        $x=$this->aton($move[1]);
        $y=intval($move[2]);
        $x2=$this->aton($move[3]);
        $y2=intval($move[4]);

        if ($x2<1||$x2>8||$y2<1||$y2>8){
            self::wrongMoveExit();
        }

        if (isset($jDesk[$x][$y][0]) && $jDesk[$x][$y][0]==$state['color'] && $jDesk[$x][$y][1]==$piece) return true;
        else {
            return false;
        }

    }

    private static function wrongMoveExit(){
        return redirect()->back()->withErrors(['error' => 'wrong piece selection']);
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

    private function checkMove($desk, $move, $state){
        $piece=$move[0];
        if ($piece=='p');
        elseif ($piece=='r');
        elseif ($piece=='n');
        elseif ($piece=='b');
        elseif ($piece=='q');
        elseif ($piece=='k');
        else self::wrongMoveExit();
    }

    private function PawnMove(&$desk,$move,$state){
        $x1=$this->aton($move[1]);
        $y1=intval($move[2]);
        $x2=$this->aton($move[3]);
        $y2=intval($move[4]);

        if ($y1!=$y2) self::wrongMoveExit();

        if ($state['color']=='w') {
            if ($x2-$x1==1 && $desk[$x2][$y2]=='') {
                $desk[$x1][$y1] = '';
                $desk[$x2][$y2] = 'wp';
            }
            elseif ($x2-$x1==2 && $desk[$x2][$y2]=='' && $desk[$x1+1][$y1+1]==''){
                $desk[$x1][$y1] = '';
                $desk[$x2][$y2] = 'wp';
            }
            else self::wrongMoveExit();
        }
        elseif ($state['color']=='b') {
            if ($x1-$x2==1 && $desk[$x2][$y2]=='') {
                $desk[$x1][$y1] = '';
                $desk[$x2][$y2] = 'wp';
            }
            elseif ($x1-$x2==2 && $desk[$x2][$y2]=='' && $desk[$x2-1][$y2-1]==''){
                $desk[$x1][$y1] = '';
                $desk[$x2][$y2] = 'bp';
            }
            else self::wrongMoveExit();
        }
    }
    private function PawnKill(&$desk,$move,$state){
        $x1=$this->aton($move[1]);
        $y1=intval($move[2]);
        $x2=$this->aton($move[3]);
        $y2=intval($move[4]);

        if ($state['color']=='w') {
            if (abs($y1-$y2) == 1 && $x2-$x1==1&&($desk[$x2][$y2][0]=='b')){
                $desk[$x1][$y1]='';
                $desk[$x2][$y2]='wp';
            }
        }
        elseif ($state['color']=='b') {
            if (abs($y1-$y2) == 1 && $x1-$x2==1&&($desk[$x2][$y2][0]=='w')){
                $desk[$x1][$y1]='';
                $desk[$x2][$y2]='bp';
            }
        }
        else self::wrongMoveExit();
    }
}
