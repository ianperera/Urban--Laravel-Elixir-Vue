<?php

namespace App\Repositories;

use DB;
use App\Models\Expense;
use App\Models\Bill;
use App\Models\BuildingHistory;
use App\Models\BuildingLocation;
use Illuminate\Config;

class ExpenseRepository
{
    protected $expenseModel;

    public function __construct(Expense $expenseModel)
    {
        $this->expenseModel = $expenseModel;
    }

    /**
     * Get expenses+sum costs based on criterias (dynamic Expense Report)
     * @param $conditions
     * @param $dimensions
     * @param $sorting
     * @return mixed
     */
    public function getByCriterias($conditions, $dimensions, $sorting)
    {
        $getExpenses = $this->expenseModel->select('expenses.*', DB::raw("SUM(cost) as sum_cost"));
        $countTotals = [ 'select' => [] ];
        $countTotals['select'][] = 'sum(sum_cost) as sum_cost';
        $groupBy = [];

        $getExpenses->with([
            'building_history' => function($query) use($conditions) {
                $query->with([
                    'building' => function($query) {
                        $query->select('id', 'serial_number');
                    },
                    'contractor' => function($query) {
                        $query->select('id', 'first_name', 'last_name');
                    },
                    'building_status' => function($query) {
                        $query->select('id', 'name', 'type');
                    }
                ]);
            },
            'building_locations' => function($query) use($conditions) {
                $query->with([
                    'building' => function($query) {
                        $query->select('id', 'serial_number');
                    },
                    'driver' => function($query) {
                        $query->select('id', 'first_name', 'last_name');
                    },
                    'location' => function($query) {
                        $query->select('id', 'name');
                    }
                ]);
            }
        ]);

        /*
         * EXPENSE TABLE RELATED FIELDS
         */

        // dimensions
        if ( isset($dimensions['bill_id']) && $dimensions['bill_id'] )
        {
            $getExpenses->with('bill');
            $groupBy[] = 'bill_id';
            $countTotals['select'][] = 'count(distinct(bill_id)) as count_bills';
        }

        if ( isset($dimensions['expense_type_item']) && $dimensions['expense_type_item'] )
        {
            $groupBy[] = 'expenses.id';
        }

        if ( isset($dimensions['expense_type']) && $dimensions['expense_type'] )
        {
            $groupBy[] = 'expense_type';
        }

        if ( $sorting['field'] == 'date' ) {
            $sorting['field'] = 'created_at';
        }

        if ( $sorting['field'] == 'expense_type_item' ) {
            $sorting['field'] = 'expenses.id';
        }



        // Dimensions (GROUP) + Conditions
        if ((isset($dimensions['building_id']) && $dimensions['building_id']) ||
            (isset($dimensions['user_id']) && $dimensions['user_id']) ||
            (isset($dimensions['expense_type_item']) && $dimensions['expense_type_item']) ||
            (isset($dimensions['date']) && $dimensions['date']) ||

            (isset($conditions['building_id']) && $conditions['building_id']) ||
            (isset($conditions['user_id']) && $conditions['user_id']) ||
            (isset($conditions['date']) && $conditions['date'])
        )
        {
            $t1 = BuildingHistory::select('building_history.id', DB::raw("'status' as type")); // morphClass
            //$t2 = BuildingLocation::select('id', DB::raw("'location' as type")); // morphClass

            /*
             * DIMENSIONS
             */
            if( isset($dimensions['date']) && $dimensions['date'] ) {
                $t1->addSelect(DB::raw('DAY(building_history.created_at) as created_at'));
                //$t2->addSelect(DB::raw('DAY(created_at) as created_at'));
                $groupBy[] = 'c.created_at';
            }

            if( isset($dimensions['user_id']) && $dimensions['user_id'] ) {
                $t1->addSelect('contractor_id as user_id')->whereNotNull('contractor_id');
                //$t2->addSelect('driver_id as user_id')->whereNotNull('driver_id');
                $getExpenses->addSelect('user_id');
                $countTotals['select'][] = 'count(distinct(user_id)) as count_users';
                $groupBy[] = 'c.user_id';
            }

            if( isset($dimensions['building_id']) && $dimensions['building_id'] ) {
                $t1->addSelect('building_id');
                //$t2->addSelect('building_id');
                $getExpenses->addSelect('building_id');
                $countTotals['select'][] = 'count(distinct(building_id)) as count_buildings';
                $groupBy[] = 'c.building_id';
            }

            /*
             * CONDITIONS
             */
            if( isset($conditions['building_id']) && $conditions['building_id'] ) {
                $t1->where('building_id', $conditions['building_id']);
                //$t2->where('building_id', $conditions['building_id']);
            }

            if( isset($conditions['user_id']) && $conditions['user_id'] ) {
                $t1->where('contractor_id', $conditions['user_id']);
                //$t2->where('driver_id', $conditions['user_id']);
            }

            if( isset($conditions['date']) && $conditions['date'] ) {
                $t1->where('created_at', '>=', $conditions['date']['start']);
                $t1->where('created_at', '<=', $conditions['date']['end']);
                //$t2->where('created_at', '>=', $conditions['date']['start']);
                //$t2->where('created_at', '<=', $conditions['date']['end']);
            }

            /*
             * Conditions (expenses table)
             * Bugs with bindings/escaping on morphed/joined table?
             * So for now - use it without laravel bindings (DB::raw)
             * TODO: WTF, need to check why it so..
             */
            if ( isset($conditions['bill_id']) && $conditions['bill_id'] ) {
                $getExpenses->where('bill_id', DB::raw("'{$conditions['bill_id']}'"));
            }

            if ( isset($conditions['expense_type']) && $conditions['expense_type'] ) {

                // only for building history (statuses)
                // Separating per status_type (extra join)
                if ( in_array($conditions['expense_type'], ['build_status', 'sale_status']) ) {
                    $expenseTypeParts = explode('_', $conditions['expense_type']);
                    $conditions['expense_type'] = $expenseTypeParts[1];
                    $conditions['status_type'] = $expenseTypeParts[0];

                    $t1->join('building_statuses', function ($join) use($conditions) {
                        $join->on('building_statuses.id', '=', 'building_history.status_id')
                             ->where('building_statuses.type', '=', $conditions['status_type']);
                    });
                }

                $getExpenses->where('expense_type', DB::raw("'{$conditions['expense_type']}'"));
            }

            $union = $t1;//->union($t2);
            $getExpenses->join(DB::raw("({$union->toSql()}) as c"), function ($join) use($conditions) {
                $join->on('expense_id', '=', 'c.id');
                $join->on('expense_type', '=', 'c.type');

                /*
                if ( isset($conditions['expense_type']) && $conditions['expense_type'] ) {
                    $join->on('expense_type', '=', DB::raw("'{$conditions['expense_type']}'"));
                }*/

            });
            $getExpenses->mergeBindings($union->getQuery());
        }

        if ( $groupBy )
            $getExpenses->groupBy($groupBy);

        // queries for count/sum TOTAL items
        $getExpensesTotals = DB::table( DB::raw("({$getExpenses->toSql()}) as sub") )->mergeBindings($getExpenses->getQuery());
        foreach($countTotals['select'] as $countTotalSelect)
            $getExpensesTotals->selectRaw($countTotalSelect);
        $expenses['totals'] = $getExpensesTotals->get();

        $getExpenses->orderBy($sorting['field'], ($sorting['direction']) ? 'desc' : 'asc');
        //DB::statement("SET @@sql_mode = REPLACE( REPLACE( REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY,', ''), ',ONLY_FULL_GROUP_BY', ''), 'ONLY_FULL_GROUP_BY', '');");
        $expenses['items'] = $getExpenses->paginate(25);
        //DB::statement("SET @@sql_mode = CONCAT(@@sql_mode, ',', 'ONLY_FULL_GROUP_BY');");
        return $expenses;
    }
}