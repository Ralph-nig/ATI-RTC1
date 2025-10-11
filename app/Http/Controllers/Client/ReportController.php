<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('client.report.index');
    }

    // RSMI Report
    public function rsmi(Request $request)
    {
        $rsmiData = collect();  
        
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $department = $request->input('department');
        $status = $request->input('status');
        
        return view('client.report.rsmi.index', compact('rsmiData'));
    }

    // RPCI Report
    public function rpci(Request $request)
    {
        return view('client.report.rpci.index');
    }

    // PPES Report
    public function ppes(Request $request)
    {
        return view('client.report.ppes.index');
    }

    // RPC PPE Report
    public function rpcPpe(Request $request)
    {
        return view('client.report.rpc-ppe.index');
    }

    public function create()
    {

    }

    public function store(Request $request)
    {

    }

    public function show($id)
    {

    }

    public function edit($id)
    {

    }

    public function update(Request $request, $id)
    {

    }

    public function destroy($id)
    {

    }
}
