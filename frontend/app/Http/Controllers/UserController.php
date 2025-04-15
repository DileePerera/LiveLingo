<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function dashboard()
    {
        $meetings = Meeting::where('host_id', session('user')->id)->get();
        // dd($meetings);
        return view('user.dashboard' ,['meetings' => $meetings]);
    }
}
