<?php

namespace Modules\Owner\Http\Controllers;

class CouponsController extends Controller {
    public function index() { return view('owner::dashboard'); }
    public function create() { return view('owner::dashboard'); }
    public function store() { return back(); }
    public function destroy($id) { return back(); }
}
