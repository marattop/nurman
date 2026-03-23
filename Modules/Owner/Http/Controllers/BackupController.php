<?php

namespace Modules\Owner\Http\Controllers;

class BackupController extends Controller {
    public function index() { return view('owner::dashboard'); }
    public function create() { return back(); }
    public function download($id) { return back(); }
    public function destroy($id) { return back(); }
}
