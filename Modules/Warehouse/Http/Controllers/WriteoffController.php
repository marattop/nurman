<?php

namespace Modules\Warehouse\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WriteoffController extends Controller
{
    public function index()
    {
        return view('warehouse::writeoff.index');
    }

    public function create()
    {
        return view('warehouse::writeoff.create');
    }

    public function store(Request $request)
    {
        // TODO: Реализовать логику создания списания

        return redirect()->route('warehouse.writeoff.index')
            ->with('success', 'Списание успешно создано');
    }

    public function pending()
    {
        return view('warehouse::writeoff.pending');
    }
}
