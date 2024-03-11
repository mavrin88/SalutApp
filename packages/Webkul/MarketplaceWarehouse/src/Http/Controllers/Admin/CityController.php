<?php

namespace  Webkul\MarketplaceWarehouse\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Webkul\MarketplaceWarehouse\Http\Controllers\Admin\Controller;
use Webkul\MarketplaceWarehouse\Repositories\CityRepository;
use Webkul\MarketplaceWarehouse\DataGrids\Admin\CityDataGrid;

class CityController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\MarketplaceWarehouse\Repositories\CityRepository $city
     * @return void
     */
    public function __construct(
       protected CityRepository $city
    ) {
        $this->_config = request('_config');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(CityDataGrid::class)->toJson();
        }

        return view($this->_config['view']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view($this->_config['view']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->city->create(request()->all());

        session()->flash('success', trans('marketplace_warehouse::app.admin.cities.create-success'));

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $city  = $this->city->find($id);

        return view($this->_config['view'], compact('city'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $city  = $this->city->find($id);

        $data  = request()->all();

        $city->update($data);

        session()->flash('success', trans('marketplace_warehouse::app.admin.cities.update-success'));

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $city  = $this->city->delete($id);

        session()->flash('success', trans('marketplace_warehouse::app.admin.cities.delete-success'));

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * To mass delete the customer
     *
     * @return \Illuminate\Http\Response
     */
    public function massDelete()
    {
        $cityIds = explode(',', request()->input('indexes'));

        foreach ($cityIds as $cityId) {
            $this->city->delete($cityId);
        }

        session()->flash('success', trans('marketplace_warehouse::app.admin.cities.delete-success'));

        return redirect()->back();
    }
}