<?php

namespace Webkul\Marketplace\Http\Controllers\Shop\Account;

use Webkul\Marketplace\Http\Controllers\Shop\Controller;
use Webkul\Marketplace\Repositories\MpProductRepository as Product;
use Webkul\Inventory\Repositories\InventorySourceRepository as InventorySource;
use Webkul\Marketplace\Repositories\ProductRepository as SellerProduct;
use Webkul\Marketplace\Repositories\SellerRepository as Seller;
use Webkul\Marketplace\Repositories\ProductDownloadableLinkRepository;
use Webkul\Marketplace\Repositories\ProductDownloadableSampleRepository;
use Webkul\Marketplace\Models\SellerProductType;
use Webkul\BookingProduct\Repositories\BookingProductRepository;
use Webkul\BookingProduct\Helpers\DefaultSlot as DefaultSlotHelper;
use Webkul\BookingProduct\Helpers\AppointmentSlot as AppointmentSlotHelper;
use Webkul\BookingProduct\Helpers\RentalSlot as RentalSlotHelper;
use Webkul\BookingProduct\Helpers\EventTicket as EventTicketHelper;
use Webkul\BookingProduct\Helpers\TableSlot as TableSlotHelper;

/**
 * Assign Product controller
 *
 * @author    Jitendra Singh <jitendra@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class AssignProductController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * InventorySourceRepository object
     *
     * @var array
     */
    protected $inventorySource;

    /**
     * ProductRepository object
     *
     * @var object
     */
    protected $product;

    /**
     * ProductRepository object
     *
     * @var object
     */
    protected $sellerProduct;

    /**
     * productDownloadableSampleRepository object
     *
     * @var object
     */
    protected $productDownloadableSampleRepository;

    /**
     * productDownloadableLinkRepository object
     *
     * @var array
     */
    protected $productDownloadableLinkRepository;

    /**
     * productDownloadableLinkRepository object
     *
     * @var array
     */
    protected $bookingProductRepository;

    /**
     * SellerRepository object
     *
     * @var object
     */
    protected $seller;

    /**
     * @return array
     */
    protected $bookingHelpers = [];

    public function __construct(
        InventorySource $inventorySource,
        Product $product,
        SellerProduct $sellerProduct,
        Seller $seller,
        ProductDownloadableLinkRepository $productDownloadableLinkRepository,
        ProductDownloadableSampleRepository $productDownloadableSampleRepository,
        BookingProductRepository $bookingProductRepository,
        DefaultSlotHelper $defaultSlotHelper,
        AppointmentSlotHelper $appointmentSlotHelper,
        RentalSlotHelper $rentalSlotHelper,
        EventTicketHelper $eventTicketHelper,
        TableSlotHelper $tableSlotHelper
    ) {
        $this->inventorySource = $inventorySource;

        $this->product = $product;

        $this->sellerProduct = $sellerProduct;

        $this->seller = $seller;

        $this->productDownloadableLinkRepository = $productDownloadableLinkRepository;

        $this->productDownloadableSampleRepository = $productDownloadableSampleRepository;

        $this->bookingProductRepository = $bookingProductRepository;

        $this->bookingHelpers['default'] = $defaultSlotHelper;

        $this->bookingHelpers['appointment'] = $appointmentSlotHelper;

        $this->bookingHelpers['rental'] = $rentalSlotHelper;

        $this->bookingHelpers['event'] = $eventTicketHelper;

        $this->bookingHelpers['table'] = $tableSlotHelper;

        $this->_config = request('_config');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $seller = $this->seller->findOneWhere(['customer_id' => auth()->guard('customer')->user()->id])->toArray();

        foreach ($seller as $key => $sellerInput) {
            if (
                $key == 'shop_title'
                && $sellerInput == null
            ) {
                return redirect()->back()->with('warning', __('marketplace::app.shop.sellers.account.profile.validation.shop_title'));
            }
            if (
                $key == 'address1'
                && $sellerInput == null
            ) {
                return redirect()->back()->with('warning', __('marketplace::app.shop.sellers.account.profile.validation.shop_title'));
            }
            if (
                $key == 'phone'
                && $sellerInput == null
            ) {
                return redirect()->back()->with('warning', __('marketplace::app.shop.sellers.account.profile.validation.phone'));
            }
            if (
                $key == 'state'
                && $sellerInput == null
            ) {
                return redirect()->back()->with('warning', __('marketplace::app.shop.sellers.account.profile.validation.state'));
            }
            if (
                $key == 'city'
                && $sellerInput == null
            ) {
                return redirect()->back()->with('warning', __('marketplace::app.shop.sellers.account.profile.validation.city'));
            }
            if (
                $key == 'country'
                && $sellerInput == null
            ) {
                return redirect()->back()->with('warning', __('marketplace::app.shop.sellers.account.profile.validation.country'));
            }
            if (
                $key == 'postcode'
                && $sellerInput == null
            ) {
                return redirect()->back()->with('warning', __('marketplace::app.shop.sellers.account.profile.validation.postcode'));
            }
        }

        if (request()->input('query')) {
            $results = [];

            foreach ($this->sellerProduct->searchProducts(request()->input('query')) as $row) {
                $results[] = [
                    'id' => $row->product_id,
                    'sku' => $row->sku,
                    'name' => $row->name,
                    'price' => core()->convertPrice($row->price),
                    'formated_price' => core()->currency($row->price),
                    'base_image' => $row->product->base_image_url,
                ];
            }

            return response()->json($results);
        } else {
            return view($this->_config['view']);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $seller = $this->seller->findOneByField('customer_id', auth()->guard('customer')->user()->id);

        $product = $this->sellerProduct->findOneWhere([
            'product_id' => $id,
            'marketplace_seller_id' => $seller->id,
        ]);

        if ($product) {
            session()->flash('error', 'You are already selling this product..');

            return redirect()->route('marketplace.account.products.search');
        }

        $baseProduct = $product = $this->product->find($id);

        if ($baseProduct != NULL) {

            $sellerAllowedProductType = SellerProductType::SellerAssignProductType($seller->id);

            if (empty($sellerAllowedProductType)) {
                $sellerAllowedProductType = ["simple", "configurable", "virtual", "downloadable"];;
            }

            if (in_array($baseProduct->type, $sellerAllowedProductType)) {
                $inventorySources = core()->getCurrentChannel()->inventory_sources;

                return view($this->_config['view'], compact('baseProduct', 'inventorySources', 'product'));
            } else {
                session()->flash('error', trans('marketplace::app.shop.marketplace.product_not_allowed', ['producttype' => $baseProduct->type]));

                return redirect()->back();
            }
        } else {
            session()->flash('error', trans('marketplace::app.shop.marketplace.something_went_wrong'));

            return redirect()->back();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function store($id)
    {
        $maxVideoFileSize = core()->getConfigData('catalog.products.attribute.file_attribute_upload_size') ?: '2048';

        $this->validate(request(), [
            'condition'   => 'required',
            'description' => 'required',
            'images.*'    => ['nullable', 'file', 'mimetypes:image/jpg,image/jpeg,image/png,image/gif,image/webp'],
            'videos.*'    => ['nullable', 'mimetypes:application/octet-stream,video/mp4,video/webm,video/quicktime', 'max:' . $maxVideoFileSize]
        ], [
            'images.*.mimetypes' => trans('marketplace::app.shop.sellers.account.profile.validation.image-type'),
            'videos.*.max'  => trans('admin::app.catalog.products.video-size', ['size' => $maxVideoFileSize]),
        ]);

        $data = array_merge(request()->all(), [
            'product_id' => $id,
            'is_owner' => 0,
        ]);

        $this->sellerProduct->createAssign($data);

        session()->flash('success', 'Product assigned successfully.');

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = $this->sellerProduct->findorFail($id);

        $inventorySources = core()->getCurrentChannel()->inventory_sources;

        return view($this->_config['view'], compact('product', 'inventorySources'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $maxVideoFileSize = core()->getConfigData('catalog.products.attribute.file_attribute_upload_size') ?: '2048';

        $this->validate(request(), [
            'condition'   => 'required',
            'description' => 'required',
            'images.*'    => ['nullable', 'file', 'mimetypes:image/jpg,image/jpeg,image/png,image/gif,image/webp'],
            'videos.*'    => ['nullable', 'mimetypes:application/octet-stream,video/mp4,video/webm,video/quicktime', 'max:' . $maxVideoFileSize]
        ], [
            'images.*.mimetypes' => trans('marketplace::app.shop.sellers.account.profile.validation.image-type'),
            'videos.*.max'  => trans('admin::app.catalog.products.video-size', ['size' => $maxVideoFileSize]),
        ]);

        $data = request()->all();

        $this->sellerProduct->updateAssign($data, $id);

        session()->flash('success', 'Assigned product updated successfully.');

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * Uploads downloadable file
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function uploadLink($id)
    {
        return response()->json(
            $this->productDownloadableLinkRepository->upload(request()->all(), $id)
        );
    }

    /**
     * Uploads downloadable sample file
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function uploadSample($id)
    {
        return response()->json(
            $this->productDownloadableSampleRepository->upload(request()->all(), $id)
        );
    }

    public function searchProduct()
    {
        if (request()->input('query')) {
            $results = [];

            foreach ($this->sellerProduct->searchProductsToAdd(request()->input('query')) as $row) {
                $results[] = [
                    'id' => $row->product_id,
                    'sku' => $row->sku,
                    'name' => $row->name,
                    'price' => core()->convertPrice($row->price),
                    'formated_price' => core()->currency($row->price),
                    'base_image' => $row->product->base_image_url,
                ];
            }

            return response()->json($results);
        } else {
            return response()->json([
                'status' => 400,
            ]);
        }
    }
}
