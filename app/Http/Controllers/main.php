<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class main extends Controller
{

    public function show(){
        if (!Auth::check())
        return view('mainView');

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

        $jdesk=array_map(null, ...$jdesk);

        foreach ($jdesk as $line) {
            foreach ($line as $item){
                echo $item;
                if ($item=="") echo "#";
            }

            echo '<br>';
        }






        return view('gameView',['desk'=>$jdesk]);

    }
}
