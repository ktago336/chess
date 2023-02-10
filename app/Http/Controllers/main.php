<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Map;

class main extends Controller
{

    public function show(){
        if (!Auth::check()) {
            return view('mainView');
        }

        else{

            $gameIDs = DB::table('games')
                ->where('black_id','=',Auth::id())
                ->orWhere('white_id','=',Auth::id())
                ->get();

            return view('listOfGames',['id'=>Auth::id(),'games'=>$gameIDs]);
        }
    }


    public function showGame($id){
        $game = DB::table('games')
            ->where('id','=',$id)
            ->first();
        $jdesk=$game->desk;

        $jdesk=json_decode($jdesk, true);

//        $jdesk=array_map(null, ...$jdesk);
//        foreach ($jdesk as $line) {
//            foreach ($line as $item){
//                echo $item.'&#9&#9';
//                if ($item=="") echo "#&#9&#9";
//            }
//            echo '<br>';
//        }

        if (Auth::id()==$game->white_id) {
            $color='w';
        }
        elseif (Auth::id()==$game->black_id) {
            $color='b';
        }

        return view('gameView',['desk'=>$jdesk, 'gameID'=>$game->id, 'color'=>$color]);
    }

    public function giveUp(){
        $gameID=explode("/",url()->previous())[4];
        $id=Auth::id();

        $gameInfo=DB::table('games')
            ->where('id','=',$gameID)
            ->first();

        if ($id==$gameInfo->white_id) {
            DB::table('games')->where('id', '=', $gameID)
                ->update(['white_won' => 1 , 'ended'=>1]);
        }
        elseif ($id==$gameInfo->black_id) {
            DB::table('games')->where('id', '=', $gameID)
                ->update(['black_won' => 1 , 'ended'=>1]);
        }
        return redirect('/');//@TODO redirect back + game archive results
    }
}
