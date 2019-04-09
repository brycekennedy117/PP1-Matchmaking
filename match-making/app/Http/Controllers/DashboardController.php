<?php

namespace App\Http\Controllers;

use App\MingleLibrary\Models\UserAttributes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**   
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $attributes = UserAttributes::find(Auth::user()->id);
        if ($attributes != null) {
            return view('dashboard');
        }
        return view('attributes');
    }
}
