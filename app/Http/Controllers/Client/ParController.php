<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Equipment;

class ParController extends Controller
{
    public function index()
    {
        $equipment = Equipment::where('unit_value', '>=', 50000)
            ->where('document_type', 'PAR')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('client.report.par.index', compact('equipment'));
    }

    public function print($id)
    {
        $equipment = Equipment::findOrFail($id);
        return view('client.report.par.print', compact('equipment'));
    }
}