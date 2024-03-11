<?php

namespace  Webkul\MarketplaceWarehouse\Http\Controllers\Shop;

use Webkul\Marketplace\Repositories\SellerRepository as Seller;
use Webkul\MarketplaceWarehouse\DataGrids\Shop\ReceiptDataGrid;
use Webkul\MarketplaceWarehouse\Http\Controllers\Shop\Controller;
use Webkul\MarketplaceWarehouse\Repositories\WarehouseRepository;

class WarehousePriceController extends Controller
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
     * @param \Webkul\MarketplaceWarehouse\Repositories\WarehouseRepository $warehouse
     * @return void
     */
    public function __construct(
        protected Seller $seller,
        protected WarehouseRepository $warehouse
    ) 
    {
        $this->_config = request('_config');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $isSeller = $this->seller->isSeller(auth()->guard('customer')->user()->id);

        if (! $isSeller) {
            return redirect()->route('marketplace.account.seller.create');
        }
        
        // if (request()->ajax()) {
        //     return app(WarehouseDataGrid::class)->toJson();
        // }

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
        $seller_id = auth()->guard('customer')->user()->id;
        
        $this->validate(request(), [
            'warehouse_name'        => 'required',
            'warehouse_description' => 'required',
        ]);

        $data = [
            'warehouse_name'        => request()->warehouse_name,
            'warehouse_description' => request()->warehouse_description,
            'marketplace_seller_id' => $seller_id,
        ];

        $this->warehouse->create($data);
      
        session()->flash('success', trans('marketplace_warehouse::app.shop.warehouse.create-warehouse'));

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
        $warehouse = $this->warehouse->find(request()->id);
     
        return view($this->_config['view'],compact('warehouse'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $this->validate(request(), [
            'warehouse_name'        => 'required',
            'warehouse_description' => 'required',
        ]);

        $data = request()->all();

        $this->warehouse->update($data, $id);

        session()->flash('success', trans('marketplace_warehouse::app.shop.warehouse.create-warehouse'));

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->warehouse->delete(request()->id);

        session()->flash('success', trans('marketplace_warehouse::app.shop.warehouse.delete-success'));

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * Mass Delete the products
     *
     * @return  \Illuminate\Http\Response
     */
    public function massDestroy()
    {
        $productIds = explode(',', request()->input('indexes'));

        foreach ($productIds as $productId) {
            $this->warehouse->delete($productId);
        }
    
        session()->flash('success', trans('marketplace_warehouse::app.shop.warehouse.mass-delete-success'));

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * Show the view for the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function view($id)
    {
        if (request()->ajax()) {
            return app(ReceiptDataGrid::class)->toJson();
        }

        return view($this->_config['view'], compact('id'));
    }
}



