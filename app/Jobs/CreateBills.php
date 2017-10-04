<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

use DB;
use App\Models\Bill;
use App\Models\Expense;
use App\Models\BuildingHistory;
use App\Models\BuildingLocation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Relation;

class CreateBills extends Job
{
    protected $user_id;

    /**
     * Create a new command instance.
     *
     * @param $userID
     */
    public function __construct($userID)
    {
        $this->user_id = $userID;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $user_id = $this->user_id;

        /*
         * HEY! Building Locations should not have driver_id and cost. So, it should not to be expensable
         */
        return;
        // select expenses, which doesn't have bill
        $queryExpenses = Expense::whereNull('expenses.bill_id')->with([
            'building_history' => function($query) {
                $query->with('contractor');
            },
            'building_locations' => function($query) {
                $query->with('driver');
            }
        ])->select('expenses.*');

        $t1 = BuildingHistory::select('id', DB::raw("'status' as type"))->whereNotNull('contractor_id');;
        $t2 = BuildingLocation::select('id', DB::raw("'location' as type"))->whereNotNull('driver_id');

        if ($this->user_id !== 'all') {
            $t1->where('contractor_id', $this->user_id);
            $t2->where('driver_id', $this->user_id);
        }

        $union = $t1->union($t2);
        $queryExpenses->join(DB::raw("({$union->toSql()}) as c"), function ($join) {
            $join->on('expense_id', '=', 'c.id');
            $join->on('expense_type', '=', 'c.type');
        });
        $queryExpenses->mergeBindings($union->getQuery());

        $expenses = $queryExpenses->get();

        // Build 2 arrays: $bills - for bills, $billsTypes - for collecting row ID (from tbl building_history/_locations)
        // These IDs will be used for updating {tbl}.bill_id = bills.id
        $bills = [];
        $collectBills = function(array $data) use(&$bills) {
            // sum amount per userID
            if ( !isset($bills[$data['user_id']]) ) {
                $bills[$data['user_id']] = [
                    'date' => Carbon::today()->toDateString(),
                    'user_id' => $data['user_id'],
                    'full_name' => $data['full_name'],
                    'amount' => 0
                ];
            }
            $bills[$data['user_id']]['amount'] += $data['cost'];
        };

        $billsTypes = [];
        $collectBillsTypes = function(array $data) use(&$billsTypes) {
            // collect row IDs per table (building history/locations)
            if ( !isset($billTypes[$data['user_id']]) ) {
                $billTypes[$data['user_id']][$data['bill_type']] = [];
            }
            $billsTypes[$data['user_id']][$data['bill_type']][] = $data['id'];
        };

        $expenses->each(function($item) use($collectBills, $collectBillsTypes) {
            $data = [];
            if ( $item->expense_type == 'status' )
            {
                $data['user_id'] = $item->building_history->first()->contractor->id;
                $data['full_name'] = $item->building_history->first()->contractor->full_name;
                $data['cost'] = $item->cost;
            } else
            if( $item->expense_type == 'location' )
            {
                $data['user_id'] = $item->building_locations->first()->driver->id;
                $data['full_name'] = $item->building_locations->first()->driver->full_name;
                $data['cost'] = $item->cost;
            } else
            {
                return false;
            }

            $collectBills($data);
            $collectBillsTypes([
                'id' => $item->expense_id,
                'bill_type' => $item->expense_type,
                'user_id' => $data['user_id']
            ]);
        });

        $billsSummary = [ 'total' => 0, 'count' => 0 ];
        foreach($bills as &$bill) {

            DB::transaction(function() use(&$bill, $billsTypes, &$billsSummary) {
                $billCreated = Bill::create($bill);
                $bill['id'] = $billCreated->id;
                $bill['number'] = $billCreated->number; // temporarily?

                $billTypeUser = $billsTypes[$bill['user_id']];
                // update building status changes rows
                if ( isset($billTypeUser['status']) )
                {
                    Expense::whereIn('expense_id', $billTypeUser['status'])
                        ->where('expense_type', 'status')
                        ->update(array('bill_id' => $bill['id']));
                }

                // update building locations changes rows
                if ( isset($billTypeUser['location']) )
                {
                    Expense::whereIn('expense_id', $billTypeUser['location'])
                        ->where('expense_type', 'location')
                        ->update(array('bill_id' => $bill['id']));
                }

                $billsSummary['total'] += $bill['amount']; // total amount
                $billsSummary['count'] ++; // total count bills
            });
        }

        return [
            'summary' => $billsSummary,
            'bills' => $bills
        ];
    }
}
