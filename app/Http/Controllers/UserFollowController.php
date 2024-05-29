<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Models\User;

class UserFollowController extends Controller
{
    //
    
    public function store(string $id){
        
        \Auth::user()->follow(intval($id));
        
        return back();
    }
    
    public function destroy(string $id){
        \Auth::user()->unfollow(intval($id));
        
        return back();
    }
    
    
}
