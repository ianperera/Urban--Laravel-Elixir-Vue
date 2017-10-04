<?php

namespace App\Services\Reports;

use App\Models\Bill;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Building;
use App\Validators\ReportValidator;
use App\Services\Reports\ReportService;
use App\Repositories\ExpenseRepository;

use Carbon\Carbon;

class ExpenseReportService extends ReportService
{

    /**
     * Contains data table (report)
     */
    public $data;

    public $validator;

    public $rules = [];

    /**
     * Strict array of report rules (required for building UI/checks)
     * @var array
     */
    public $rulesScheme = [
        'default' => [
            'dimensions_fixed' => ['cost'],
            'dimensions' => [ 'date', 'user_id', 'building_id', 'expense_type', 'expense_type_item', 'bill_id' ],

            // conditions
            'fields' => [ 'date', 'user_id', 'building_id', 'expense_type', 'bill_id' ],
        ],
        'admin' => [ ]
    ];

    /**
     * Strict array of report dimensions (dynamically specifed by user)
     * @var array
     */
    public $dimensionAttributes = [
        'date' => ['title' => 'Date', 'index' => 1],
        'user_id' => ['title' => 'User', 'index' => 2],
        'building_id' => ['title' => 'Building', 'index' => 3],
        'expense_type' => ['title' => 'Expense Type', 'index' => 4],
        'expense_type_item' => ['title' => 'Expense Item', 'index' => 5],
        'cost' => ['title' => 'Cost', 'index' => 6],
        'bill_id' => ['title' => 'Bill Number', 'index' => 7],
    ];

    /**
     * Strict array of report condtions (dynamically specifed by user)
     * @var array
     */
    public $conditionAttributes = [
        'date' => ['title' => 'Date', 'index' => 1],
        'user_id' => ['title' => 'User', 'index' => 2],
        'building_id' => ['title' => 'Building', 'index' => 3],
        'expense_type' => ['title' => 'Expense Type', 'index' => 4],
        'bill_id' => ['title' => 'Bill Number', 'index' => 6]
    ];

    /**
     * Strict array of report totals
     * @var array
     */
    public $totalAttributes = [
        'count_users' => ['title' => 'Total Users', 'index' => 1],
        'count_buildings' => ['title' => 'Total Buildings', 'index' => 2],
        'count_bills' => ['title' => 'Total Bills', 'index' => 2],
        'sum_cost' => ['title' => 'Total Cost', 'index' => 3]
    ];

    protected function getConditions(array $inputConditions) {
        $conditions = [];

        // Check and set defaults conditions/dimensions
        if ( isset($inputConditions['date']) ) {
            $conditions['date']['start'] = date('Y-m-d 00:00:00', strtotime($inputConditions['date']['start']));
            $conditions['date']['end'] = date('Y-m-d 23:59:59', strtotime($inputConditions['date']['end']));
        }

        if ( isset($inputConditions['user_id']) ) $conditions['user_id'] = $inputConditions['user_id'];
        if ( isset($inputConditions['building_id']) ) $conditions['building_id'] = $inputConditions['building_id'];
        if ( isset($inputConditions['expense_type']) ) $conditions['expense_type'] = $inputConditions['expense_type'];
        if ( isset($inputConditions['bill_id']) ) $conditions['bill_id'] = $inputConditions['bill_id'];

        return $conditions;
    }

    protected function getDimensions(array $inputDimensions) {
        $dimensions = [];

        if ( isset($inputDimensions['building_id']) ) $dimensions['building_id'] = 'building_id';
        if ( isset($inputDimensions['user_id']) ) $dimensions['user_id'] = 'user_id';
        if ( isset($inputDimensions['date']) ) $dimensions['date'] = 'date';
        if ( isset($inputDimensions['bill_id']) ) $dimensions['bill_id'] = 'bill_id';
        if ( isset($inputDimensions['expense_type']) ) $dimensions['expense_type'] = 'expense_type';
        if ( isset($inputDimensions['expense_type_item']) ) $dimensions['expense_type_item'] = 'expense_type_item';

        return $dimensions;
    }

    /**
     * Get rules for build UI
     * @return array
     */
    public function getRules()
    {
        $dimensions = $this->getDimensionAttributes($this->rulesScheme['default']['dimensions']);
        $conditions = $this->getConditionAttributes($this->rulesScheme['default']['fields']);

        if ( isset($conditions['user_id']) )
        {
            $users = User::all()->pluck('full_name', 'id');
            $conditions['user_id']['data'] = $users;
            $conditions['user_id']['disabled'] = (count($users) > 0) ? false : true;
        }

        if ( isset($conditions['building_id']) )
        {
            $buildings = Building::all()->pluck('serial_number', 'id');
            $conditions['building_id']['data'] = $buildings;
            $conditions['building_id']['disabled'] = (count($buildings) > 0) ? false : true;
        }

        if ( isset($conditions['expense_type']) )
        {
            $expense_types = [
                'location' => 'Location',
                'build_status' => 'Build Status',
            ];
            $conditions['expense_type']['data'] = $expense_types;
            $conditions['expense_type']['disabled'] = (count($expense_types) > 0) ? false : true;
        }

        if ( isset($conditions['bill_id']) )
        {
            $bills = Bill::all()->pluck('number', 'id');
            $conditions['bill_id']['data'] = $bills;
            $conditions['bill_id']['disabled'] = (count($bills) > 0) ? false : true;
        }

        $conditions['date']['start'] = Carbon::now()->startOfMonth()->toDateString();
        $conditions['date']['start_formatted'] = date('F j, Y', strtotime($conditions['date']['start']));
        $conditions['date']['end'] = Carbon::now()->endOfDay()->toDateString();
        $conditions['date']['end_formatted'] = date('F j, Y', strtotime($conditions['date']['end']));

        $this->rules['timestamp'] = true;
        $this->rules['tz_offset'] = 0;
        $this->rules['report_type'] = 'expenses';
        $this->rules['dimensions'] = $dimensions;
        $this->rules['conditions'] = $conditions;
        $this->rules['defaults']['dimensions'] = array('date', 'user_id');
        $this->rules['defaults']['conditions'] = array('date');

        return $this->rules;
    }

    /**
     * Request + parse data, return mixed (data + html)
     * @param Request $request
     * @param ExpenseRepository $expenseRepository
     * @return $this
     */
    public function getReport(Request $request, ExpenseRepository $expenseRepository)
    {
        $inputAll = $request->all();
        $this->validator = ReportValidator::make($inputAll)->scope('expenses');

        if ( $this->validator->fails() ) {
            return $this;
        }

        $conditions = $this->getConditions($request->input('conditions', []));
        $dimensions = $this->getDimensions($request->input('dimensions', []));
        $sorting = $this->getSorting($request->input('sort', []));

        // Build Data + Pagination
        $stats = $expenseRepository->getByCriterias($conditions, $dimensions, $sorting);
        $statsHead = $this->getDimensionAttributes($dimensions, $this->rulesScheme['default']['dimensions_fixed'], $sorting);
        $statsData = $stats['items'];
        $statsTotals = $stats['totals'];

        if ( $statsData->count() ) {
            $this->parseStatsbyDimensions($dimensions, $statsData);

            $this->data['stats_table']['data'] = $statsData->toArray()['data'];
            $this->data['stats_table_pagination'] = $statsData->render();
        }

        if ( !is_null($statsTotals) )
        {
            $totalAtts = [];
            foreach($statsTotals as &$totals) {
                $totals->sum_cost = '$'.number_format($totals->sum_cost, 2, '.', ',');

                if( isset($totals->count_users)) $totalAtts[] = 'count_users';
                if( isset($totals->count_bills)) $totalAtts[] = 'count_bills';
                if( isset($totals->count_buildings)) $totalAtts[] = 'count_buildings';
                if( isset($totals->sum_cost)) $totalAtts[] = 'sum_cost';
            }

            $this->data['stats_table']['totals']['params'] = $this->getTotalAttributes($totalAtts);
            $this->data['stats_table']['totals']['items'] = $statsTotals[0];
        }

        $this->data['stats_table']['head'] = $statsHead;

        return $this;
    }

    private function parseStatsbyDimensions(array $dimensions, &$statsData) {

        foreach ($statsData as &$expense) {

            if ($expense['expense_type'] == 'location') {
                $expense_row = $expense->building_locations->first();
                $expense_row_user = $expense_row->driver;
            } else
            if ($expense['expense_type'] == 'status') {
                $expense_row = $expense->building_history->first();
                $expense_row_user = $expense_row->contractor;
            }

            if (isset($dimensions['building_id'])) {
                if (isset($expense_row->building)) {
                    $expense['building'] = [
                        'id' => $expense_row->building->id,
                        'serial_number' => $expense_row->building->serial_number
                    ];
                } else
                    $expense['building'] = null;
            }

            if (isset($dimensions['expense_type'])) {
                $expense['expense_type_group'] = true;

                if ( $expense['expense_type'] == 'status' ) {
                    $expense['expense_type_formatted'] = ucfirst("{$expense_row->building_status->type} Status");
                } else {
                    $expense['expense_type_formatted'] = ucfirst($expense['expense_type']);
                }
            }

            if (isset($dimensions['expense_type_item'])) {
                $expense['expense_type_item_group'] = true;
            }

            if (isset($dimensions['date'])) {
                $expense['date_formatted'] = date('F j, Y', strtotime($expense_row->created_at));
            }

            if (isset($dimensions['user_id'])) {
                $expense['user'] = [
                    'full_name' => $expense_row_user->full_name
                ];
            }

            $expense['cost_formatted'] = number_format($expense->sum_cost, 2, '.', ',');
        }
    }

}
