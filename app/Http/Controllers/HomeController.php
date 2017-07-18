<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 2017/7/18
 * Time: 19:37
 */

namespace App\Http\Controllers;


class HomeController extends Controller
{
    public function index() {
        return view('home.index');
    }
}