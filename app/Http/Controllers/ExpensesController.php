<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB;
use Auth;
use App\Models\User;
use App\Models\Bill;
use App\Models\Expense;
use App\Models\Building;
use App\Models\BuildingHistory;
use App\Models\BuildingLocation;

use App\Commands\CreateBills;
use App\Http\Requests\AddBillRequest;
use Mockery\CountValidator\Exception;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Collection;

use App\Services\Reports\ExpenseReportService;

class ExpensesController extends Controller
{
    private $route_name = 'expenses';

    private $params = [];

    public function __construct()
    {
        $this->params = [
            'breadcrumbs' => [
                ['url' => '/', 'page' => 'Expenses']
            ],
            'title' => 'Expenses',
            'subtitle' => 'Expenses',
            'search' => 'yes',
            'filter' => '',
            'items_per_page' => 500,
            'route_name' => $this->route_name,
            'route' => '/' . $this->route_name,
            'data' => null,
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @param ReportService|ExpenseReportService $expenseReport
     * @return \Illuminate\Http\Response
     */
    public function report(ExpenseReportService $expenseReport)
    {
        array_push($this->params['breadcrumbs'], ['url' => '', 'page' => 'Report']);

        $rules['expense_report'] = $expenseReport->getRules();

        $this->params['rules'] = $rules;
        $this->params['subtitle'] = 'Expense Report';
        return view($this->route_name . '.report')->withParams($this->params);
    }
}
