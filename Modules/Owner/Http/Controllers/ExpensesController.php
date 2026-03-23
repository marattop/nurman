<?php

namespace Modules\Owner\Http\Controllers;

class ExpensesController extends Controller {
    public function index() { return view('owner::dashboard'); }
    public function create() { return view('owner::dashboard'); }
    public function store() { return back(); }
}
