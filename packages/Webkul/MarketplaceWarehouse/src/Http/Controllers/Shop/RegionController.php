<?php

namespace  Webkul\MarketplaceWarehouse\Http\Controllers\Shop;

use Webkul\MarketplaceWarehouse\Http\Controllers\Shop\Controller;
use Webkul\Marketplace\Repositories\SellerRepository as Seller;
use Webkul\MarketplaceWarehouse\DataGrids\Shop\RegionDataGrid;
use Webkul\MarketplaceWarehouse\Repositories\PriceTypeRepository;
use Webkul\MarketplaceWarehouse\Repositories\DeliveryTypeRepository;
use Webkul\MarketplaceWarehouse\Repositories\DeliveryTimeRepository;
use Webkul\MarketplaceWarehouse\Repositories\CityRepository;
use Webkul\MarketplaceWarehouse\Repositories\WarehouseRepository;
use Webkul\MarketplaceWarehouse\Repositories\RegionRepository;
use Webkul\MarketplaceWarehouse\Repositories\AssignCityRepository;
use Webkul\MarketplaceWarehouse\Repositories\DeliveryChargeRepository;
use Webkul\MarketplaceWarehouse\DataGrids\Shop\RegionRecordDataGrid;
use Webkul\MarketplaceWarehouse\Repositories\DiscountRepository;


class   RegionController extends Controller
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
     * @param  \Webkul\Marketplace\Repositories\SellerRepository  $seller
     * @param  \Webkul\MarketplaceWarehouse\Repositories\WarehouseRepository $warehouse
     * @param  \Webkul\MarketplaceWarehouse\Repositories\PriceTypeRepository $priceType
     * @param  \Webkul\MarketplaceWarehouse\Repositories\DeliveryTypeRepository $deliveryType
     * @param  \Webkul\MarketplaceWarehouse\Repositories\DeliveryTimeRepository $deliveryTime
     * @param  \Webkul\MarketplaceWarehouse\Repositories\CityRepository $city
     * @param  \Webkul\MarketplaceWarehouse\Repositories\RegionRepository $region
     * @param  \Webkul\MarketplaceWarehouse\Repositories\AssignCityRepository $assignCity
     * @param  \Webkul\MarketplaceWarehouse\Repositories\DeliveryChargeRepository $deliveryCharge
     * @param  \Webkul\MarketplaceWarehouse\Repositories\DiscountRepository $discount
     * @return void
     */
    public function __construct(
        protected Seller $seller,
        protected WarehouseRepository $warehouse,
        protected PriceTypeRepository $priceType,
        protected DeliveryTypeRepository $deliveryType,
        protected DeliveryTimeRepository $deliveryTime,
        protected CityRepository $city,
        protected RegionRepository $region,
        protected AssignCityRepository $assignCity,
        protected DeliveryChargeRepository $deliveryCharge,
        protected DiscountRepository $discount
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
        $isSeller = $this->seller->isSeller(auth()->guard('customer')->user()->id);

        if (! $isSeller) {
            return redirect()->route('marketplace.account.seller.create');
        }

        if (request()->ajax()) {
            return app(RegionDataGrid::class)->toJson();
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
        $seller = $this->seller->findOneByField('customer_id', auth()->guard('customer')->user()->id);

        $warehouses = $this->warehouse->findByField('marketplace_seller_id', $seller->id);

        $priceTypes = $this->priceType->findByField('marketplace_seller_id', $seller->id);

        $deliveryTypes = $this->deliveryType->all();

        $deliveryTimes = $this->deliveryTime->all();

        $cities = $this->city->all();

        $regionIds = $this->region->findByField('marketplace_seller_id', $seller->id);

        $cityIds = [];

        if ($regionIds) {
            foreach ($regionIds as $region) {
                $regionId = $region->id;

                $Ids = $this->assignCity->findByField('warehouse_region_id', $regionId)->pluck('city_id');

                $cityIds = array_merge($cityIds, $Ids->toArray());
            }

            $cities = $this->city->findWhereNotIn('id',$cityIds);
        }

        return view($this->_config['view'], compact('warehouses', 'priceTypes', 'deliveryTypes', 'deliveryTimes', 'cities'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $seller_id = auth()->guard('customer')->user()->id;

        $warehouse = $this->warehouse->findOneWhere([
            'warehouse_name'        => request()->input('warehouse_name'),
            'marketplace_seller_id' => $seller_id
        ]);

        $warehouseId = $warehouse ? $warehouse->id : null;

        $priceType = $this->priceType->findOneWhere([
            'title'                 => request()->price_type,
            'marketplace_seller_id' => $seller_id
        ]);

        $priceTypeId = $priceType ? $priceType->id : null;

        $deliveryType = $this->deliveryType->findOneByField('title', request()->delivery_type);

        $deliveryTypeId = $deliveryType ? $deliveryType->id : null;

        $deliveryTime = $this->deliveryTime->findOneByField('title', request()->delivery_time);

        $deliveryTimeId = $deliveryTime ? $deliveryTime->id : null;

        $data = [
            'region_name'               => request()->region_name,
            'delivery_type_id'          => $deliveryTypeId,
            'delivery_time_id'          => $deliveryTimeId,
            'max_weight'                => request()->max_weight,
            'warehouse_id'              => $warehouseId,
            'price_type_id'             => $priceTypeId,
            'marketplace_seller_id'     => $seller_id,
        ];

        $regionData = $this->region->create($data);

        $discountData = $this->discount->create($regionData);

        $assignedCities = request()->assigned_cities;

        foreach ($assignedCities as $city)
        {
            $cityId = $this->city->findOneByField('name', $city)->id;

            $data = [
                'warehouse_region_id'   => $regionData->id,
                'city_id'               => $cityId,
            ];

            $this->assignCity->create($data);
        }

        $deliveries = request()->delivery;

        if (! $deliveries) {
            session()->flash('success', trans('marketplace_warehouse::app.shop.warehouse.create-warehouse'));

            return redirect()->route($this->_config['redirect'], ['id' => $regionData->id]);
        } else {
            foreach ($deliveries as $delivery)
            {
                $data = [
                    'from'                  => $delivery['from'],
                    'to'                    => $delivery['to'],
                    'cost'                  => $delivery['cost'],
                    'warehouse_region_id'   => $regionData->id,
                ];

                $this->deliveryCharge->create($data);
            }
        }

        session()->flash('success', trans('marketplace_warehouse::app.shop.warehouse.create-warehouse'));

        return redirect()->route($this->_config['redirect'], ['id' => $regionData->id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $seller = $this->seller->findOneByField('customer_id', auth()->guard('customer')->user()->id);

        $warehouses = $this->warehouse->findByField('marketplace_seller_id', $seller->id);

        $priceTypes = $this->priceType->findByField('marketplace_seller_id', $seller->id);

        $deliveryTypes = $this->deliveryType->all();

        $deliveryTimes = $this->deliveryTime->all();

        $cities = $this->city->all();

        $regionIds = $this->region->findByField('marketplace_seller_id', $seller->id);

        $cityIds = [];

        if ($regionIds) {
            foreach ($regionIds as $region) {
                if ($region->id == $id)
                continue;

                $regionId = $region->id;

                $Ids = $this->assignCity->findByField('warehouse_region_id', $regionId)->pluck('city_id');

                $cityIds = array_merge($cityIds, $Ids->toArray());
            }

            $cities = $this->city->findWhereNotIn('id',$cityIds);
        }

        $region = $this->region->find($id);

        $regionWarehouse = $this->warehouse->find($region->warehouse_id);

        $regionPriceType = $this->priceType->find($region->price_type_id);

        $regionDeliveryType = $this->deliveryType->find($region->delivery_type_id);

        $regionDeliveryTime = $this->deliveryTime->find($region->delivery_time_id);

        $regionCityIds = $this->assignCity->findByField('warehouse_region_id', $id)->pluck('city_id')->toArray();

        $deliveryData = $this->deliveryCharge->findByField('warehouse_region_id', $id);

        return view($this->_config['view'],compact('warehouses', 'priceTypes', 'deliveryTypes', 'deliveryTimes', 'cities', 'region', 'regionWarehouse', 'regionPriceType', 'regionDeliveryType', 'regionDeliveryTime', 'regionCityIds', 'deliveryData'));
    }

    /**
     * Update a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $seller_id = auth()->guard('customer')->user()->id;

        $region = $this->region->findOrFail($id);

        if ($region) {
            $priceType = $this->priceType->findOneWhere([
                'title'                 => request()->price_type,
                'marketplace_seller_id' => $seller_id
            ]);

            $priceTypeId = $priceType ? $priceType->id : null;

            $deliveryType = $this->deliveryType->findOneByField('title', request()->delivery_type);

            $deliveryTypeId = $deliveryType ? $deliveryType->id : null;

            $deliveryTime = $this->deliveryTime->findOneByField('title', request()->delivery_time);

            $deliveryTimeId = $deliveryTime ? $deliveryTime->id : null;

            $data = [
                'delivery_type_id'          => $deliveryTypeId,
                'delivery_time_id'          => $deliveryTimeId,
                'max_weight'                => request()->max_weight,
                'price_type_id'             => $priceTypeId,
                'marketplace_seller_id'     => $seller_id,
            ];

            dd($data);

            $regionData = $this->region->update($data, $id);

            $this->discount->updateData($regionData);

            $assignedCities = request()->assigned_cities;

            foreach ($assignedCities as $city)
            {
                $cityId = $this->city->findOneByField('name', $city)->id;

                $data = [
                    'warehouse_region_id'   => $regionData->id,
                    'city_id'               => $cityId,
                ];

                $city = $this->assignCity->findOneByField($data);

                if (! $city) {
                    $this->assignCity->create($data);
                }

                $cityIds[] = $cityId;
            }

            $this->assignCity
                ->whereNotIn('city_id', $cityIds)
                ->where('warehouse_region_id', $id)
                ->delete();

            $deliveryCharges = $this->deliveryCharge->findByField('warehouse_region_id', $id);

            if ($deliveryCharges->count() > 0) {
                $this->deliveryCharge->where('warehouse_region_id', $id)->delete();
            }

            $deliveries = request()->delivery;

            if ($deliveries) {
                foreach ($deliveries as $delivery)
                {
                    $data = [
                        'from'                  => $delivery['from'],
                        'to'                    => $delivery['to'],
                        'cost'                  => $delivery['cost'],
                        'warehouse_region_id'   => $regionData->id,
                    ];

                    $this->deliveryCharge->create($data);
                }
            }

            session()->flash('success',  trans('marketplace_warehouse::app.shop.warehouse.region.update-success'));

            return redirect()->route($this->_config['redirect']);
        }

        session()->flash('error', trans('marketplace_warehouse::app.shop.warehouse.region.update-failed'));

        return redirect()->back();
    }

    public function updateNew($id)
    {
        $seller_id = auth()->guard('customer')->user()->id;

        $region = $this->region->findOrFail($id);

        if ($region) {
            $priceType = $this->priceType->findOneWhere([
                'title'                 => $region->price_type,
                'marketplace_seller_id' => $seller_id
            ]);

            $priceTypeId = $priceType ? $priceType->id : null;

            $deliveryType = $this->deliveryType->findOneByField('title', $region->delivery_type);

            $deliveryTypeId = $deliveryType ? $deliveryType->id : null;

            $deliveryTime = $this->deliveryTime->findOneByField('title', $region->delivery_time);

            $deliveryTimeId = $deliveryTime ? $deliveryTime->id : null;

            $data = [
                'delivery_type_id'          => $region->delivery_type_id,
                'delivery_time_id'          => $region->delivery_time_id,
                'max_weight'                => $region->max_weight,
                'price_type_id'             => $region->price_type_id,
                'marketplace_seller_id'     => $seller_id,
            ];

            $regionData = $this->region->update($data, $id);

            $this->discount->updateData($regionData);

            $deliveryCharges = $this->deliveryCharge->findByField('warehouse_region_id', $id);

            if ($deliveryCharges->count() > 0) {
                $this->deliveryCharge->where('warehouse_region_id', $id)->delete();
            }

            $deliveries = request()->delivery;

            if ($deliveries) {
                foreach ($deliveries as $delivery)
                {
                    $data = [
                        'from'                  => $delivery['from'],
                        'to'                    => $delivery['to'],
                        'cost'                  => $delivery['cost'],
                        'warehouse_region_id'   => $regionData->id,
                    ];

                    $this->deliveryCharge->create($data);
                }
            }
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
        $regionData = $this->region->find($id);

        if (request()->ajax()) {
            return app(RegionRecordDataGrid::class)->toJson();
        }

        return view($this->_config['view'], compact('id'));
    }

    /**
     * Update Discount.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateDiscount($id)
    {
        $discount = $this->discount->find($id);

        $discountPercentage = request()->discount;

        $price = $discount->base_selling_price;

        // Calculate the discount amount
        $discountAmount = ($discountPercentage / 100) * $price;

        // Calculate the updated price after discount
        $updatedPrice = $price - $discountAmount;

        $data = [
            'discount'              => request()->discount,
            'real_selling_price'    => $updatedPrice
        ];

        $this->discount->update($data, $id);

        return response()->json([
            'message'           => __('admin::app.catalog.products.saved-inventory-message'),
            'updatedTotal'      => $this->discount->find($id)->discount,
            'realSellingPrice'  => $updatedPrice
        ]);
    }

    /**
     * Remove Region
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function remove($id)
    {
        $this->region->delete(request()->id);

        return redirect()->route($this->_config['redirect']);
    }
}
