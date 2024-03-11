<?php

namespace  Webkul\MarketplaceWarehouse\Http\Controllers\Shop;

use Webkul\Marketplace\Repositories\SellerRepository as Seller;
use Webkul\MarketplaceWarehouse\DataGrids\Shop\PriceTypeDataGrid;
use Webkul\MarketplaceWarehouse\Http\Controllers\Shop\Controller;
use Webkul\MarketplaceWarehouse\Repositories\WarehouseRepository;
use Webkul\MarketplaceWarehouse\Repositories\PriceTypeRepository;
use Webkul\MarketplaceWarehouse\Repositories\PriceRepository;
use Webkul\Marketplace\Repositories\MpProductRepository as Product;
use Webkul\MarketplaceWarehouse\DataGrids\Shop\PriceRecordDataGrid;
use Webkul\Product\Repositories\ProductRepository as CoreProductRepository;

class PriceController extends Controller
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
     * @param  \Webkul\Marketplace\Repositories\SellerRepository $seller
     * @param  \Webkul\MarketplaceWarehouse\Repositories\WarehouseRepository $warehouse
     * @param  \Webkul\MarketplaceWarehouse\Repositories\PriceTypeRepository $priceType
     * @param  \Webkul\MarketplaceWarehouse\Repositories\PriceRepository  $price
     * @param  \Webkul\Marketplace\Repositories\MpProductRepository $product
     * @param  \Webkul\Product\Repositories\ProductRepository $coreProduct
     * @return void
     */
    public function __construct(
        protected Seller $seller,
        protected WarehouseRepository $warehouse,
        protected PriceTypeRepository $priceType,
        protected PriceRepository $price,
        protected Product $product,
        protected CoreProductRepository $coreProduct,
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
        
        if (request()->ajax()) {
            return app(PriceTypeDataGrid::class)->toJson();
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
        $seller_id = auth()->guard('customer')->user()->id;

        $links = request()->input('links');

        if (! $links){
            session()->flash('error',  trans('marketplace_warehouse::app.shop.warehouse.price.add-product'));

            return redirect()->back();
        } 

        $title = $this->priceType->findByField('marketplace_seller_id', $seller_id)->pluck('title')->toArray();
        
        if (in_array(request()->input('price_title'), $title)) {
            session()->flash('error',  trans('marketplace_warehouse::app.shop.warehouse.price.product-exist'));

            return redirect()->back();
        } else {
            $data = [
                'title'                  =>  request()->input('price_title'),
                'marketplace_seller_id'  =>  $seller_id,
            ];
    
            $priceType = $this->priceType->create($data);
          
            foreach ($links as $link) {
                $price = $link['price'];
                $productId = $link['associated_product_id'];
    
                $data = [
                    'price'           => $price,
                    'price_type_id'   => $priceType->id,
                    'product_id'      => $productId,
                ];
                
                $this->price->create($data);
            }   
        }
      
        session()->flash('success', trans('marketplace_warehouse::app.shop.warehouse.price.create-success'));
   
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
        $warehousePriceType = $this->priceType->find($id);

        $productGroupedProduct = [];

        $groupProductIds = app('Webkul\MarketplaceWarehouse\Repositories\PriceRepository')
            ->findByField('price_type_id', $warehousePriceType->id)
            ->pluck('product_id')
            ->toArray();

        $groupProductsCollection = app('Webkul\Product\Repositories\ProductRepository')
            ->find($groupProductIds);


        if ($groupProductsCollection->count() > 0) {
            $productGroupedProduct = $groupProductsCollection->filter(function ($item, $key) use ($warehousePriceType) {
            
                $groupedProductSellerPrice = \Webkul\MarketplaceWarehouse\Models\Price::where([
                    ['product_id', '=', $item->id],
                    ['price_type_id', '=', $warehousePriceType->id]
                ])->first();

                if ($groupedProductSellerPrice != null) {
                    $item->productPrice = $groupedProductSellerPrice->price;
                } else {
                    $item->productPrice = $item->price;
                }

                return $item;
            });
        }

        return view($this->_config['view'],compact('productGroupedProduct', 'warehousePriceType'));
    }

    /**
     * Update a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update()
    {
        $priceType = $this->priceType->find(request()->id);

        $seller_id = auth()->guard('customer')->user()->id;

        $links = request()->input('links');

        if (! $links){
            session()->flash('error',  trans('marketplace_warehouse::app.shop.warehouse.price.add-product'));

            return redirect()->back();
        } 

        if ($priceType->marketplace_seller_id == $seller_id) {  

            $priceType = $this->priceType->update([
                'title'   =>  request()->input('price_title'),
            ], request()->id);
        
            foreach ($links as $link) {
                $price = $link['price'];
                $productId = $link['associated_product_id'];
        
                $data = [
                    'price'           => $price,
                    'price_type_id'   => $priceType->id,
                    'product_id'      => $productId,
                ];

                $product = $this->price->findOneWhere(['product_id' => $productId, 'price_type_id' => $priceType->id]);

                if (! $product) {
                    $price = $this->price->create($data);
                } else {
                    $price = $this->price->update([
                        'price'   =>  $price
                    ], $product->id);
                }

                $priceIds[] = $price->id;
            }

            $this->price
                ->whereNotIn('id', $priceIds)
                ->where('price_type_id', $priceType->id)
                ->delete();
        }   
    
        session()->flash('success', trans('marketplace_warehouse::app.shop.warehouse.price.update-success'));
   
        return redirect()->route($this->_config['redirect']);
    }


    /**
     * Remove Price Type
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function remove($id)
    {
        $this->priceType->delete($id);
        
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
        $priceTypeId = $this->price->find($id)->price_type_id;
        
        $this->price->delete($id);

        $productPrice = $this->price->findOneByField('price_type_id',$priceTypeId);

        if(! $productPrice){
            $this->priceType->delete($priceTypeId);
        }

        return redirect()->route($this->_config['redirect'], ['id' => $id]);
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
            return app(PriceRecordDataGrid::class)->toJson();
        }

        return view($this->_config['view'], compact('id'));
    }
}



