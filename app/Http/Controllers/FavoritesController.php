<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class FavoritesController extends Controller
{
    //
    public function store(string $id){

        \Auth::user()->favorite(intval($id));
        return back();
    }
    
    public function destroy(string $id){
        
        \Auth::user()->unfavorite(intval($id));
        return back();
    }
}
