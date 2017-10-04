<?php

namespace App\Http\Controllers;

use App\Models\Dealer;
use App\Models\Location;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Requests\Dealers\AddDealerRequest;
use App\Http\Requests\Dealers\EditDealerRequest;
use App\Http\Requests\Dealers\UpdateDealerRequest;
use App\Http\Requests\Dealers\DeleteDealerRequest;

class DealersController extends Controller
{

    private $route_name = 'dealers';

    private $params = [];

    public function __construct()
    {
        $this->params = [
            'breadcrumbs' => [
                ['url' => '/', 'page' => 'Dashboard'],
                ['url' => $this->route_name, 'page' => 'Dealers']
            ],
            'title' => 'Dealers',
            'subtitle' => 'Dealers',
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
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->params['filter'] = $request->input('filter');
        $this->params['data']['items'] = Dealer::filteredPaginate($this->params['filter'], $this->params['items_per_page']);

        return view('spa')->withParams($this->params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        array_push($this->params['breadcrumbs'], ['url' => '', 'page' => 'New Dealer']);

        $this->params['subtitle'] = 'New Dealer';
        $this->params['data']['locations'] = Location::all()->pluck('name', 'id')->toArray();
        return view($this->route_name . '._form')->withParams($this->params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AddDealerRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddDealerRequest $request)
    {
        try
        {
            $dealerData = $request->only(['business_name', 'phone', 'email', 'tax_rate', 'cash_sale_deposit_rate', 'location_id']);
            $dealer = Dealer::create($dealerData);
        } catch (QueryException $e)
        {
            return redirect($this->route_name)->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'Sorry, there was an error saving the record.']);
        }

        return redirect($this->route_name)->withMessages(['title' => 'Success', 'type' => 'success', 'text' => 'Dealer added succcessfully.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param EditDealerRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(EditDealerRequest $request,  $id)
    {
        // get data which has got through validator
        $dealer = Store::get('dealer');

        $this->params['data']['item'] = $item = $dealer;
        $this->params['data']['locations'] = Location::all()->pluck('name', 'id')->toArray();
        
        array_push($this->params['breadcrumbs'], ['url' => '', 'page' => 'Edit Dealer ' . $item->business_name]);
        $this->params['subtitle'] = 'Edit Dealer ' . $item->name;
        return view($this->route_name . '._form')->withParams($this->params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateDealerRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDealerRequest $request, $id)
    {
        // get data which has got through validator
        $dealer = Store::get('dealer');

        try
        {
            $dealerData = $request->only(['business_name', 'phone', 'email', 'tax_rate', 'cash_sale_deposit_rate', 'location_id']);

            $dealer->update($dealerData);
        }
        catch (QueryException $e)
        {
            return redirect($this->route_name)->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'Sorry, there was an error updating the record.']);
        }

        return redirect($this->route_name)->withMessages(['title' => 'Success', 'type' => 'success', 'text' => 'Dealer successfully updated.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DeleteDealerRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteDealerRequest $request, $id)
    {
        try
        {
            // get data which has got through validator
            $dealer = Store::get('dealer');
            $dealer->delete();
        }
        catch (QueryException $e)
        {
            return redirect($this->route_name)->withMessages(['title' => 'Error', 'type' => 'error', 'text' => 'Sorry, there was an error updating the record.']);
        }

        return redirect($this->route_name)->withMessages(['title' => 'Success', 'type' => 'success', 'text' => 'Dealer successfully deleted.']);
    }
}
