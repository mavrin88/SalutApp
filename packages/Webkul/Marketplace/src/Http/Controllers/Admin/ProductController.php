<?php

namespace Webkul\Marketplace\Http\Controllers\Admin;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Webkul\Marketplace\Repositories\ProductRepository;
use Webkul\Marketplace\Mail\ProductApprovalNotification;
use Webkul\Marketplace\Mail\ProductDisapprovalNotification;
use Webkul\Marketplace\Repositories\MpProductRepository as Product;
use Webkul\Marketplace\DataGrids\Admin\ProductDataGrid;
use Webkul\Marketplace\DataGrids\Admin\ProductReviewsDataGrid;

/**
 * Admin Product Controller
 *
 * @author Anmol Singh Chauhan <anmol.chauhan207@webkul.in>
 * @copyright 2022 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class ProductController extends Controller
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
     * @param  Webkul\Marketplace\Repositories\ProductRepository $productRepository
     * @return void
     */
    public function __construct(
        protected ProductRepository $productRepository,
        protected Product $product
    ) {
        $this->_config = request('_config');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Mixed
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(ProductDataGrid::class)->toJson();
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
        $product = $this->productRepository->find($id);

        $sellerProduct = $this->productRepository->getMarketplaceProductByProduct($product->product_id, $product->marketplace_seller_id);

        if ($sellerProduct) {
            if ($sellerProduct->is_owner == 1) {
                $this->productRepository->delete($sellerProduct->id);
                $this->product->delete($sellerProduct->product_id);
            } else {
                $this->productRepository->delete($sellerProduct->id);
            }
        }

        return response()->json([
            'message' => trans('marketplace::app.admin.response.delete-success', ['name' => 'Product'])
        ], 200);
    }

    /**
     * Mass Delete the products
     *
     * @return response
     */
    public function massDestroy()
    {
        $productIds = explode(',', request()->input('indexes'));

        foreach ($productIds as $productId) {
            $product = $this->productRepository->find($productId);

            if ($product) {
                $sellerProduct = $this->productRepository->getMarketplaceProductByProduct($product->product_id, $product->marketplace_seller_id);

                if ($sellerProduct) {
                    if ($sellerProduct->is_owner == 1) {
                        $this->productRepository->delete($sellerProduct->id);
                        $this->product->delete($sellerProduct->product_id);
                    } else {
                        $this->productRepository->delete($sellerProduct->id);
                    }
                }
            }
        }

        session()->flash('success', trans('marketplace::app.admin.products.mass-delete-success'));

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * Mass updates the products
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

        $productIds = explode(',', $data['indexes']);

        foreach ($productIds as $productId) {
            $sellerProduct = $this->productRepository->find($productId);
            $seller = app('Webkul\Marketplace\Repositories\SellerRepository')->find($sellerProduct->marketplace_seller_id);

            if ($sellerProduct) {
                $product = $sellerProduct->product;
                if (
                    $seller->is_approved ||
                    ! (int)$data['update-options']
                ) {
                    $sellerProduct->update([
                        'is_approved' => $data['update-options']
                    ]);

                    if ($data['update-options']) {
                        try {
                            Mail::send(new ProductApprovalNotification($sellerProduct));
                        } catch (\Exception $e) {
                        }
                    } else {
                        try {
                            Mail::send(new ProductDisapprovalNotification($sellerProduct));
                        } catch (\Exception $e) {
                        }
                    }
                } else {
                    session()->flash('error', trans('marketplace::app.admin.products.mass-update-disable') . " " . $sellerProduct->product->name);
                }
            }
        }

        session()->flash('success', trans('marketplace::app.admin.products.mass-update-success'));

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * Products Reviews
     *
     * @return response
     */
    public function reviews()
    {
        if (request()->ajax()) {
            return app(ProductReviewsDataGrid::class)->toJson();
        }

        return view($this->_config['view']);
    }
}
