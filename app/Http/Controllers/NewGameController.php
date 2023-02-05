<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Redirect;

class NewGameController extends Controller
{
    public function newGame(Request $request){
        $validated=$request->validate([
            'name' => 'required'
        ]);

        $opponentId=$validated["name"];

        if ($opponentId==Auth::id()){
            return Redirect::back()->withErrors(['msg' => 'Sorry, no solo play supported yet']);

        }

        $defaultDesk=[
            '1'=>'a',
            '2'=>'b'
        ];
        $defaultDesk=file_get_contents('defaultDesk.json');

        if (DB::table('users')->where('id', $opponentId)->exists()) {
            DB::table('games')->insert([
                'white_id' => Auth::id(),
                'black_id'=>$opponentId,
                'ended'=>0,
                'turn_id'=>Auth::id(),
                'desk'=>$defaultDesk //@TODO default start desk
            ]);
            return Redirect::back();
        }
        else{
            return Redirect::back()->withErrors(['msg' => 'No such ID found']);
        }
    }
}
