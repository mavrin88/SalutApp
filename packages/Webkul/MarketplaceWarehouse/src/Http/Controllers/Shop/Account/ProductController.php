<?php

namespace Webkul\MarketplaceWarehouse\Http\Controllers\Shop\Account;

use Webkul\Product\Helpers\ProductType;
use Webkul\Marketplace\Http\Controllers\Shop\Account\ProductController as MarketplaceProductController;

/**
 * Product controller
 *
 * @author    Jitendra Singh <jitendra@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class ProductController extends MarketplaceProductController
{
    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        if (
            !request()->get('family')
            && ProductType::hasVariants(request()->input('type'))
            && request()->input('sku') != ''
        ) {
            return redirect(url()->current() . '?type=' . request()->input('type') . '&family=' . request()->input('attribute_family_id') . '&sku=' . request()->input('sku'));
        }

        if (
            ProductType::hasVariants(request()->input('type'))
            && (!request()->has('super_attributes')
                || !count(request()->get('super_attributes')))
        ) {
            session()->flash('error', trans('admin::app.catalog.products.configurable-error'));

            return back();
        }

        $this->validate(request(), [
            'type' => 'required',
            'attribute_family_id' => 'required',
            'sku' => 'required'
        ]);

        $sellerAllowedProductTypes = $this->getAllowedProducts();

        if (!$sellerAllowedProductTypes->has(request()->input('type'))) {
            session()->flash('errot', 'Warning: You are not allowed to add ' . request()->input('type') . 'type of product.');

            return redirect()->back();
        }

        $product = $this->product->create(request()->all());

        $this->sellerProduct->create([
            'product_id' => $product->id,
            'is_owner' => 1
        ]);

        session()->flash('success', 'Product created successfully.');

        return redirect()->route($this->_config['redirect'], ['id' => $product->id]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Webkul\Product\Http\Requests\ProductForm $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $maxVideoFileSize = core()->getConfigData('catalog.products.attribute.file_attribute_upload_size') ?: '2048';

        $this->validate(request(), [
            'images.files.*' => ['nullable', 'file', 'mimetypes:image/jpg,image/jpeg,image/png,image/gif,image/webp'],
            'videos.files.*' => ['nullable', 'mimetypes:application/octet-stream,video/mp4,video/webm,video/quicktime', 'max:' . $maxVideoFileSize]
        ], [
            'images.files.*.mimetypes' => trans('marketplace::app.shop.sellers.account.profile.validation.image-type'),
            'videos.files.*.max' => trans('admin::app.catalog.products.video-size', ['size' => $maxVideoFileSize]),
        ]);

        $data = request()->all();


        $sellerProducts = $this->sellerProduct->getMarketplaceProductByProduct($id);
        if (!$sellerProducts) {
            session()->flash('error', 'Unautorized, Product is not related to seller.');

            return redirect()->route($this->_config['redirect']);
        }

        $data['url_key'] = str_slug($data['name']);
        $productSlug = $this->product->find($id)->url_key;
        if($data['url_key'] != $productSlug) {
            $prefix = 1;
            while ($this->coreProduct->findBySlug($data['url_key'])) {
                $data['url_key'] = $data['url_key'] . $prefix;
                $prefix++;
            }
        }

        $this->product->update($data, $id);

        $this->sellerProduct->update(request()->all(), $sellerProducts->id);

        session()->flash('success', 'Product updated successfully.');

        return redirect()->route($this->_config['redirect']);
    }
}
