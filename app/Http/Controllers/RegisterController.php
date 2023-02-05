<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use DB;
class RegisterController extends Controller
{
    public function reg(Request $request){
        $request->validate([
            'email'=>'required',
            'password'=>'required',
            'name'=>'required',
        ]);

        $name=$request->input('name');
        $email=$request->input('email');
        $password=Hash::make($request->input('password'));
        $user=DB::table('users')
            ->where('name','=',$name)
            ->orWhere('email', $email)
            ->first();

        if (isset($user->name))
            return redirect()->back()->withErrors(['email'=>'EMAIL OR USERNAME AlREADY IN USE']);
        else {
            DB::insert('insert into users (name, email, password) values (?, ?, ?)',[$name, $email, $password]);
            return redirect()->to('/');}

    }
}
