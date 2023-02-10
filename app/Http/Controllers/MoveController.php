<?php

namespace App\Http\Controllers;


use http\Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use \App\Game;

class MoveController extends Controller
{
    public function move(Request $request){
        echo "<script>console.log('aaaaaaa' );</script>";
        $move = $request->validate([
            'move' => ['required'],
        ]);
        #redirect()->back()->withErrors(['error'=>'not your turn']);
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
            'white_checked'=>$gameInfo->white_checked,
            'black_checked'=>$gameInfo->black_checked,
            'black_can_000'=>$gameInfo->black_can_000,
            'black_can_00'=>$gameInfo->black_can_00,
            'white_can_000'=>$gameInfo->white_can_000,
            'white_can_00'=>$gameInfo->white_can_00,
            'game_id'=>$gameID,

        );


        if ($id!=$gameInfo->turn_id){
            return redirect()->back()->withErrors(['error'=>'not your turn']);
        }

        $game->move($desk, $request->input('move'),$state);

        if ($desk!=0) {
            DB::table('games')->where('id', '=', $gameID)
                ->update(['desk' => json_encode($desk),
                    'black_can_000'=>$state['black_can_000'],
                    'black_can_00'=>$state['black_can_00'],
                    'white_can_000'=>$state['white_can_000'],
                    'white_can_00'=>$state['white_can_00'],
                    'white_checked'=>$state['white_checked'],
                    'black_checked'=>$state['black_checked']
                    ]); //'turn_id' => $opponentID,  //@TODO
        }
        //@todo WHO CHECKED->DB

        return redirect()->back();
    }
}
