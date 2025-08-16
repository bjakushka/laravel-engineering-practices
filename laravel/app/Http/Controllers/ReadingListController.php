<?php

namespace App\Http\Controllers;

class ReadingListController extends Controller
{
    public function index()
    {
        return view('reading_list.index');
    }
}
