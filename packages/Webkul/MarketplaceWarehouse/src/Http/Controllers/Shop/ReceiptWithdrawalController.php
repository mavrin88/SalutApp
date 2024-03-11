<?php

namespace Webkul\MarketplaceWarehouse\Http\Controllers\Shop;

use Webkul\MarketplaceWarehouse\Http\Controllers\Shop\Controller;
use Webkul\Marketplace\Repositories\SellerRepository as Seller;
use Webkul\MarketplaceWarehouse\Repositories\RegionRepository;
use Webkul\MarketplaceWarehouse\Repositories\WarehouseRepository;
use Webkul\MarketplaceWarehouse\Repositories\ReceiptRepository;
use Webkul\Product\Repositories\ProductInventoryIndexRepository as ProductIndices;
use Webkul\MarketplaceWarehouse\Repositories\QtyLogRepository;
use Webkul\Marketplace\Repositories\OrderRepository as Order;
use Webkul\MarketplaceWarehouse\DataGrids\Shop\ReceiptDataGrid;
use Webkul\MarketplaceWarehouse\DataGrids\Shop\ReceiptRecordDataGrid;

class ReceiptWithdrawalController extends Controller
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
     * @param  \Webkul\MarketplaceWarehouse\Repositories\ReceiptRepository $receipt
     * @param  \Webkul\Product\Repositories\ProductInventoryIndexRepository $productIndices
     * @param  \Webkul\MarketplaceWarehouse\Repositories\QtyLogRepository $qtyLog
     * @param  \Webkul\Marketplace\Repositories\OrderRepository $order
     * @return void
     */
    public function __construct(
        protected Seller $seller,
        protected WarehouseRepository $warehouse,
        protected ReceiptRepository $receipt,
        protected QtyLogRepository $qtyLog,
        protected ProductIndices $productIndices,
        protected Order $order,
        protected RegionRepository $region,
        protected RegionController $regionController,
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
            return app(ReceiptDataGrid::class)->toJson();
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
        $warehouses = $this->warehouse->get()
            ->where('marketplace_seller_id', auth()->guard('customer')->user()->id);

        return view($this->_config['view'],compact('warehouses'));
    }

    /**
     * Search seller products.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProducts()
    {
        $seller = $this->seller->findOneByField('customer_id', Auth()->guard('customer')->user()->id);

        $products = $this->receipt->getSimpleProducts($seller->id);

        return $products;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $seller_id = auth()->guard('customer')->user()->id;

        $warehouse = $this->warehouse
            ->findOneWhere([
                'warehouse_name'        => request()->input('warehouse_name'),
                'marketplace_seller_id' => $seller_id]);

        $warehouseId = $warehouse ? $warehouse->id : null;

        $data = [
            'title'         =>  request()->input('title'),
            'warehouse_id'  =>  $warehouseId,
        ];

        $receipt = $this->receipt->create($data);

        $links = request()->input('links');

        $receiptId = $receipt->id;

        foreach ($links as $link) {
            $associatedProductId = $link['associated_product_id'];
            $qty = $link['qty'];

            $availableQty = $this->productIndices->findOneByField('product_id', $associatedProductId)->qty;

            if ($qty>0) {
                $updatedQty = $availableQty + $qty;
            } else {
                $updatedQty = $availableQty  - abs($qty);
            }

            $this->productIndices->where('product_id', $associatedProductId)->update(['qty' => $updatedQty]);

            $data = [
                'associated_product_id' => $associatedProductId,
                'qty'                   => $qty,
                'receipt_id'            => $receiptId,
            ];

            $this->qtyLog->create($data);
        }


        $totalElements = count($links);
        $totalQty = array_sum(array_column($links, 'qty'));

        $data = [
            'title' => "Артикулов " . $totalElements . ", товарных единиц " . $totalQty,
        ];

        $this->receipt->update($data, $receiptId);


        $this->saveRegionAfterStore($seller_id);

        session()->flash('success', trans('marketplace_warehouse::app.shop.warehouse.receipt-and-withdrawal.created-receipt-and-withdrawal'));

        return redirect()->route($this->_config['redirect']);
    }

    public function saveRegionAfterStore($seller_id)
    {
        $sellerRegions = $this->region->where('marketplace_seller_id', $seller_id)->get();

        foreach ($sellerRegions as $sellerRegion) {
            $this->regionController->updateNew($sellerRegion->id);
        }
    }


        /**
     * Show the view for the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function view($id)
    {
        $receipt = $this->receipt->find($id);

        $warehouseId = $receipt->warehouse_id;

        $warehouse = $this->warehouse->find($warehouseId)->warehouse_name;

        if (request()->ajax()) {
            return app(ReceiptRecordDataGrid::class)->toJson();
        }

        return view($this->_config['view'], compact('receipt', 'warehouse'));
    }
}
