<?php

namespace  Webkul\MarketplaceWarehouse\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Webkul\MarketplaceWarehouse\Http\Controllers\Admin\Controller;
use Webkul\MarketplaceWarehouse\Repositories\DeliveryTimeRepository;
use Webkul\MarketplaceWarehouse\DataGrids\Admin\DeliveryTimeDataGrid;

class DeliveryTimeController extends Controller
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
     * @param  \Webkul\MarketplaceWarehouse\Repositories\DeliveryTimeRepository $deliveryTime
     * @return void
     */
    public function __construct(
       protected DeliveryTimeRepository $deliveryTime
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
            return app(DeliveryTimeDataGrid::class)->toJson();
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->deliveryTime->create(request()->all());

        session()->flash('success', trans('marketplace_warehouse::app.admin.delivery-time.create-success'));

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
        $deliveryTime  = $this->deliveryTime->find($id);

        return view($this->_config['view'], compact('deliveryTime'));
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
        $deliveryTime  = $this->deliveryTime->find($id);

        $data  = request()->all();

        $deliveryTime->update($data);

        session()->flash('success', trans('marketplace_warehouse::app.admin.delivery-time.update-success'));

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
        $deliveryTime  = $this->deliveryTime->delete($id);

        session()->flash('success', trans('marketplace_warehouse::app.admin.delivery-time.delete-success'));

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * To mass delete the customer
     *
     * @return \Illuminate\Http\Response
     */
    public function massDelete()
    {
        $deliveryTimeIds = explode(',', request()->input('indexes'));

        foreach ($deliveryTimeIds as $deliveryTimeId) {
            $this->deliveryTime->delete($deliveryTimeId);
        }

        session()->flash('success', trans('marketplace_warehouse::app.admin.delivery-time.delete-success'));

        return redirect()->back();
    }
}