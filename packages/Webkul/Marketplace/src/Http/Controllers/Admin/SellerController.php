<?php

namespace Webkul\Marketplace\Http\Controllers\Admin;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Webkul\Marketplace\Repositories\SellerRepository;
use Webkul\Marketplace\Mail\SellerApprovalNotification;
use Webkul\Marketplace\Repositories\ProductRepository as SellerProduct;
use Webkul\Product\Repositories\ProductRepository as Product;
use Webkul\Marketplace\Mail\SellerDisapprovalNotification;
use Webkul\Marketplace\Models\SellerProductType;
use Webkul\Marketplace\DataGrids\Admin\SellerDataGrid;

/**
 * Admin Seller Controller
 *
 * @author Anmol Singh Chauhan <anmol.chauhan207@webkul.in>
 * @copyright 2022 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class SellerController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    public function __construct(
        protected SellerRepository $sellerRepository,
        protected SellerProduct $sellerProduct,
        protected Product $product
    ) {
        $this->_config = request('_config');
    }

    /**
     * Display seller create form.
     *
     * @return Mixed
     */
    public function create()
    {
        return view($this->_config['view']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Mixed
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(SellerDataGrid::class)->toJson();
        }

        return view($this->_config['view']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $seller = $this->sellerRepository->findOrFail($id);

        try {
            $this->sellerRepository->delete($id);

            session()->flash('success', trans('admin::app.response.delete-success', ['name' => 'Seller']));

            return response()->json(['message' => trans('admin::app.response.delete-success', ['name' => 'Seller'])], 200);
        } catch (\Exception $e) {
            report($e);

            session()->flash('error', trans('admin::app.response.delete-failed', ['name' => 'Seller']));
        }

        return response()->json(['message' => false], 400);
    }

    /**
     * Mass Delete the sellers
     *
     * @return response
     */
    public function massDestroy()
    {
        $sellerIds = explode(',', request()->input('indexes'));

        foreach ($sellerIds as $sellerId) {
            $this->sellerRepository->delete($sellerId);
        }

        session()->flash('success', trans('marketplace::app.admin.sellers.mass-delete-success'));

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * Mass updates the sellers
     *
     * @return response
     */
    public function massUpdate()
    {
        $data = request()->all();
        
        if (
            ! isset($data['mass-action-type']) ||
            ! $data['mass-action-type'] == 'update'
        ) {
            return redirect()->back();
        }

        $sellerIds = explode(',', $data['indexes']);

        foreach ($sellerIds as $sellerId) {
            $seller = $this->sellerRepository->find($sellerId);
            $seller['all_pro'] = true;
            $sellerProduct = $this->sellerProduct->findAllBySeller($seller);

            foreach ($sellerProduct as $_sellerProduct) {
                $sellerProduct = $this->sellerProduct->getMarketplaceProductByProduct($_sellerProduct->product_id, $sellerId);
                $sellerProduct->update(['is_approved' => $data['update-options']]);
            }

            unset($seller['all_pro']);
            $seller->update(['is_approved' => $data['update-options']]);

            if ($data['update-options']) {

                try {
                    Mail::send(new SellerApprovalNotification($seller));
                } catch (\Exception $e) {
                }
            } else {

                try {
                    Mail::send(new SellerDisapprovalNotification($seller));
                } catch (\Exception $e) {
                }
            }
        }

        session()->flash('success', trans('marketplace::app.admin.sellers.mass-update-success'));

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $sellerId
     * @return \Illuminate\Http\Response
     */
    public function search($id)
    {
        $seller = $this->sellerRepository->findOneWhere([
            'id' => $id
        ]);

        if ($seller) {
            $seller = $seller->toArray();
        } else {
            return abort(404);
        }

        foreach ($seller as $key => $sellerInput) {
            if (
                $key == 'shop_title' &&
                $sellerInput == null
            ) {
                session()->flash('warning', __('marketplace::app.shop.sellers.account.profile.validation.shop_title'));

                return redirect()->back();
            }
            if (
                $key == 'address1' &&
                $sellerInput == null
            ) {
                session()->flash('warning', __('marketplace::app.shop.sellers.account.profile.validation.shop_title'));

                return redirect()->back();
            }
            if (
                $key == 'phone' &&
                $sellerInput == null
            ) {
                session()->flash('warning', __('marketplace::app.shop.sellers.account.profile.validation.phone'));

                return redirect()->back();
            }
            if (
                $key == 'state' &&
                $sellerInput == null
            ) {
                session()->flash('warning', __('marketplace::app.shop.sellers.account.profile.validation.state'));

                return redirect()->back();
            }
            if (
                $key == 'city' &&
                $sellerInput == null
            ) {
                session()->flash('warning', __('marketplace::app.shop.sellers.account.profile.validation.city'));

                return redirect()->back();
            }
            if (
                $key == 'country' &&
                $sellerInput == null
            ) {
                session()->flash('warning', __('marketplace::app.shop.sellers.account.profile.validation.country'));

                return redirect()->back();
            }
            if (
                $key == 'postcode' &&
                $sellerInput == null
            ) {
                session()->flash('warning', __('marketplace::app.shop.sellers.account.profile.validation.postcode'));

                return redirect()->back();
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
                    'formated_price' => core()->currency(core()->convertPrice($row->price)),
                    'base_image' => $row->product->base_image_url,
                ];
            }

            return response()->json($results);
        } else {
            return view($this->_config['view'], compact('id'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $sellerId,  $productId
     * @return \Illuminate\Http\Response
     */

    public function assignProduct($sellerId, $productId)
    {
        $product = $this->sellerProduct->findOneWhere([
            'product_id' => $productId,
            'marketplace_seller_id' => $sellerId
        ]);

        if ($product == NULL) {
            $baseProduct = $product = $this->product->find($productId);

            if ($baseProduct != NULL) {
                $sellerAllowedProductType = SellerProductType::SellerAssignProductType($sellerId);

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
                session()->flash('error', trans('marketplace::app.shop.marketplace.this_product_is_not_in_our_db'));

                return redirect()->back();
            }
        } else {
            session()->flash('error', trans('marketplace::app.shop.marketplace.already_selling'));

            return redirect()->back();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  int  $sellerId,  $productId
     * @return \Illuminate\Http\Response
     */
    public function saveAssignProduct($sellerId, $productId)
    {
        $this->validate(request(), [
            'condition' => 'required',
            'description' => 'required'
        ]);

        $data = array_merge(request()->all(), [
            'product_id' => $productId,
            'is_owner' => 0,
            'seller_id' => $sellerId
        ]);

        $sellerAllowedProductType = SellerProductType::SellerAssignProductType($sellerId);

        if (empty($sellerAllowedProductType)) {
            $sellerAllowedProductType = ["simple", "configurable", "virtual", "downloadable"];
        }

        if (in_array(request()->product_type, $sellerAllowedProductType)) {
            $product = $this->sellerProduct->createAssign($data);
            session()->flash('success', 'Product Assigned successfully.');

            return redirect()->route($this->_config['redirect']);
        } else {
            session()->flash('error', trans('marketplace::app.shop.marketplace.product_not_allowed', ['producttype' => 'booking product type']));

            return redirect()->back();
        }
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
        $this->validate(request(), [
            'condition' => 'required',
            'description' => 'required'
        ]);

        $data = request()->all();

        $this->sellerProduct->updateAssign($data, $id);

        session()->flash('success', 'Assigned product updated successfully.');

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * Edit seller profile
     */
    public function editProfile($id)
    {
        $seller =  $this->sellerRepository->find($id);
        $allProductTypes = SellerProductType::SellerAssignedProductType($id);

        return view($this->_config['view'], compact('seller', 'allProductTypes'));
    }

    /**
     * Update seller profile
     */
    public function updateProfile($id)
    {
        $seller =  $this->sellerRepository->find($id);
        $data = request()->all();

        if ($seller) {
            if (! isset($data['commission_enable'])) {
                $data['commission_enable']     = 0;
                $data['commission_percentage'] = 0;
            }

            $this->sellerRepository->update($data, $id);
        }

        /*Assign product type to seller */
        DB::table('seller_producttype')->where('marketplace_sellers_id', $id)->delete();

        if (
            (! empty(request()->seller_product_type)) &&
            (count(request()->seller_product_type) > 0)
        ) {
            $sellerProductTypeArr = [];
            foreach (request()->seller_product_type as $productType) {
                $sellerProductTypeArr[] = [
                    'marketplace_sellers_id' => $id,
                    'product_type' => $productType
                ];
            }

            DB::table('seller_producttype')->insert($sellerProductTypeArr);
        }

        session()->flash('success', __('marketplace::app.admin.sellers.update-success'));

        return redirect()->route($this->_config['redirect']);
    }

    public function sellerApprovedUnapproved($id)
    {
        $seller =  $this->sellerRepository->find($id);

        $seller->update(['is_approved' => request()->get('is_approved')]);

        return response()->json([
            'message'      => __('marketplace::app.admin.sellers.update-success'),
            'getStatus' => $seller->is_approved ?
                __('marketplace::app.admin.sellers.approved') :
                __('marketplace::app.admin.sellers.un-approved'),
        ]);
    }
}
