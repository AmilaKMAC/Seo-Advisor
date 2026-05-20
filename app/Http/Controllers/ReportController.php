<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
    use App\Models\SeoReport;


class ReportController extends Controller
{

public function show(SeoReport $report)
{
    return view('pages.report', compact('report'));
}
}
