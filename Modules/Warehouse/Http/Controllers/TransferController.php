<?php

namespace Modules\Warehouse\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    public function index()
    {
        return view('warehouse::transfers.index');
    }

    public function create()
    {
        return view('warehouse::transfers.create');
    }

    public function store(Request $request)
    {
        // TODO: Реализовать логику создания перемещения

        return redirect()->route('warehouse.transfers.index')
            ->with('success', 'Перемещение успешно создано');
    }
}
