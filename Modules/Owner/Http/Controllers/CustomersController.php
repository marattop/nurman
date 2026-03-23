<?php

namespace Modules\Owner\Http\Controllers;

class CustomersController extends Controller {
    public function index() { return view('owner::dashboard'); }
    public function show($id) { return view('owner::dashboard'); }
    public function update($id) { return back(); }
}
