<?php

namespace Webkul\Marketplace\Http\Controllers\Shop;

use Illuminate\Support\Facades\Storage;
use Webkul\Marketplace\Repositories\SellerRepository;
use Webkul\Marketplace\Repositories\ProductRepository;
use Webkul\Marketplace\Repositories\ProductDownloadableSampleRepository;
use Webkul\Marketplace\Repositories\ProductDownloadableLinkRepository;

use Webkul\Product\Repositories\ProductRepository as BaseProductRepository;

/**
 * Marketplace product controller
 *
 * @author    Jitendra Singh <jitendra@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class ProductController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    public function __construct(
        protected SellerRepository $seller,
        protected ProductRepository $product,
        protected BaseProductRepository $baseProduct,
        protected ProductDownloadableSampleRepository  $productDownloadableSampleRepository,
        protected ProductDownloadableLinkRepository  $productDownloadableLinkRepository
    ) {
        $this->_config = request('_config');
    }

    /**
     * Method to populate the seller product page which will be populated.
     *
     * @param  string  $url
     * @return Mixed
     */
    public function index($url)
    {
        $seller = $this->seller->findByUrlOrFail($url);
        $product = $this->product->findAllBySeller($seller);

        return view($this->_config['view'], compact('seller', 'product'));
    }

    /**
     * Product offers by sellers
     *
     * @param  integer $id
     * @return Mixed
     */
    public function offers($id)
    {
        $product = $this->baseProduct->findOrFail($id);

        if ($product->type == 'configurable') {
            session()->flash('error', trans('shop::app.checkout.cart.integrity.missing_options'));

            return redirect()->route('shop.productOrCategory.index', ['slug' => $product->url_key]);
        }

        return view($this->_config['view'], compact('product'));
    }

    /**
     * Download the for the specified resource.
     *
     * @return \Illuminate\Http\Response|\Exception
     */
    public function downloadSample()
    {
        try {
            if (request('type') == 'link') {
                $productDownloadableLink = $this->productDownloadableLinkRepository->findOrFail(request('id'));

                if ($productDownloadableLink->sample_type == 'file') {
                    $privateDisk = Storage::disk('private');

                    return $privateDisk->exists($productDownloadableLink->sample_file)
                        ? $privateDisk->download($productDownloadableLink->sample_file)
                        : abort(404);
                }

                $fileName = $name = substr($productDownloadableLink->sample_url, strrpos($productDownloadableLink->sample_url, '/') + 1);

                $tempImage = tempnam(sys_get_temp_dir(), $fileName);

                copy($productDownloadableLink->sample_url, $tempImage);

                return response()->download($tempImage, $fileName);
            }

            $productDownloadableSample = $this->productDownloadableSampleRepository->findOrFail(request('id'));

            if ($productDownloadableSample->type == 'file') {
                return Storage::download($productDownloadableSample->file);
            }

            $fileName = $name = substr($productDownloadableSample->url, strrpos($productDownloadableSample->url, '/') + 1);

            $tempImage = tempnam(sys_get_temp_dir(), $fileName);

            copy($productDownloadableSample->url, $tempImage);

            return response()->download($tempImage, $fileName);
        } catch (\Exception $e) {
            abort(404);
        }
    }
}
