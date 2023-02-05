<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use \App\Game;

class MoveController extends Controller
{
    public function move(Request $request){
        $move = $request->validate([
            'move' => ['required'],
        ]);

        $gameID=explode("/",url()->previous())[4];

        $game=new Game();
        $gameInfo=DB::table('games')
            ->where('id','=',$gameID)
            ->first();

        $desk=json_decode($gameInfo->desk,true);
        $id=Auth::id();



        if ($id==$gameInfo->white_id) {
            $color = 'w';
            $opponentID=$gameInfo->black_id;
            $passAbility=$gameInfo->white_can_take_on_pass;

        }
        elseif ($id==$gameInfo->black_id) {
            $color = 'b';
            $opponentID=$gameInfo->white_id;
            $passAbility=$gameInfo->black_can_take_on_pass;

        }
        else {echo "Error, your id is not equal to white or black id in this game, if you sure that everything is right, contact support";
            return 0;
        }

        $state=array('color'=>$color,
            'id'=>$id,
            'pass'=>$passAbility,
        );


        if ($id!=$gameInfo->turn_id){
            return redirect()->back()->withErrors(['error'=>'not your turn']);
        }



        $desk=$game->move($desk, $request->input('move'),$state);
        if ($desk!=0) {
            DB::table('games')->where('id', '=', $gameID)
                ->update(['desk' => json_encode($desk), 'turn_id' => $opponentID]);
        }
        //@todo WHO CHECKED->DB
        return redirect()->back();
    }
}
