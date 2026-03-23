<?php

namespace Modules\Owner\Http\Controllers;

class UsersController extends Controller {
    public function index() { return view('owner::dashboard'); }
    public function create() { return view('owner::dashboard'); }
    public function store() { return back(); }
    public function edit($id) { return view('owner::dashboard'); }
    public function update($id) { return back(); }
    public function destroy($id) { return back(); }
}
