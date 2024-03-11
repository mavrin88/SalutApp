<?php

namespace  Webkul\MarketplaceWarehouse\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Webkul\MarketplaceWarehouse\Http\Controllers\Admin\Controller;
use Webkul\MarketplaceWarehouse\Repositories\DeliveryTypeRepository;
use Webkul\MarketplaceWarehouse\DataGrids\Admin\DeliveryTypeDataGrid;

class DeliveryTypeController extends Controller
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
     * @param  \Webkul\MarketplaceWarehouse\Repositories\DeliveryTypeRepository $deliveryType
     * @return void
     */
    public function __construct(
       protected DeliveryTypeRepository $deliveryType
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
            return app(DeliveryTypeDataGrid::class)->toJson();
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
        $this->deliveryType->create(request()->all());

        session()->flash('success', trans('marketplace_warehouse::app.admin.delivery-type.create-success'));

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
        $deliveryType  = $this->deliveryType->find($id);

        return view($this->_config['view'], compact('deliveryType'));
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
        $deliveryType  = $this->deliveryType->find($id);

        $data  = request()->all();

        $deliveryType->update($data);

        session()->flash('success', trans('marketplace_warehouse::app.admin.delivery-type.update-success'));

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
        $deliveryType  = $this->deliveryType->delete($id);

        session()->flash('success', trans('marketplace_warehouse::app.admin.delivery-type.delete-success'));

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * To mass delete the customer
     *
     * @return \Illuminate\Http\Response
     */
    public function massDelete()
    {
        $deliveryTypeIds = explode(',', request()->input('indexes'));

        foreach ($deliveryTypeIds as $deliveryTypeId) {
            $this->deliveryType->delete($deliveryTypeId);
        }

        session()->flash('success', trans('marketplace_warehouse::app.admin.delivery-type.delete-success'));

        return redirect()->back();
    }
}