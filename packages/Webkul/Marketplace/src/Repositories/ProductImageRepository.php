<?php

namespace Webkul\Marketplace\Repositories;

use Illuminate\Http\UploadedFile;
use Webkul\Core\Eloquent\Repository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Container\Container as App;
use Webkul\Product\Repositories\ProductRepository;

class ProductImageRepository extends Repository
{
    /**
     * Create a new repository instance.
     *
     * @param  \Webkul\Product\Repositories\ProductRepository $productRepository
     * @param  \Illuminate\Container\Container                $app
     * @return void
     */
    public function __construct(
        protected ProductRepository $productRepository,
        App $app
    ) {
        parent::__construct($app);
    }

    /**
     * Specify model class name.
     *
     * @return string
     */
    public function model(): string
    {
        return 'Webkul\Marketplace\Contracts\ProductImage';
    }

    /**
     * Get product directory.
     *
     * @param  Webkul\Product\Models\Product $variant
     */
    public function getProductDirectory($product): string
    {
        return 'product/' . $product->id;
    }

    /**
     * Upload images.
     *
     * @param  array  $data
     * @param  \Webkul\Product\Models\Product  $product
     * @return void
     */
    public function uploadImages($data, $product): void
    {
        $previousImageIds = $product->images()->pluck('id');

        if (isset($data['images'])) {
            foreach ($data['images'] as $imageId => $image) {
                $file = 'images.' . $imageId;
                $dir = 'product/' . $product->id;

                if (str_contains($imageId, 'image_')) {
                    if (request()->hasFile($file)) {
                        $this->create([
                                'path' => request()->file($file)->store($dir),
                                'marketplace_product_id' => $product->id
                            ]);
                    }
                } else {
                    if (is_numeric($index = $previousImageIds->search($imageId))) {
                        $previousImageIds->forget($index);
                    }

                    if (request()->hasFile($file)) {
                        if ($imageModel = $this->find($imageId)) {
                            Storage::delete($imageModel->path);
                        }
                    }
                }
            }
        }

        foreach ($previousImageIds as $imageId) {
            if ($imageModel = $this->find($imageId)) {
                Storage::delete($imageModel->path);

                $this->delete($imageId);
            }
        }
    }

    /**
     * Upload.
     *
     * @param  Webkul\Product\Models\Product $product
     * @param  array
     * @return void
     */
    public function upload($product, $images): void
    {
        $previousVariantImageIds = $product->images()->pluck('id');

        if ($images) {
            foreach ($images as $imageId => $image) {
                if ($image instanceof UploadedFile) {
                    
                    app('Webkul\Product\Repositories\ProductImageRepository')->create([
                        'path'       => $image->store($this->getProductDirectory($product)),
                        'product_id' => $product->id,
                    ]);
                } else {
                    if (is_numeric($index = $previousVariantImageIds->search($imageId))) {
                        $previousVariantImageIds->forget($index);
                    }
                }
            }
        }

        foreach ($previousVariantImageIds as $imageId) {
            if ($image = app('Webkul\Product\Repositories\ProductImageRepository')->find($imageId)) {
                Storage::delete($image->path);

                app('Webkul\Product\Repositories\ProductImageRepository')->delete($imageId);
            }
        }
    }

    /**
     * Upload variant images.
     *
     * @param  array $variants
     * @return void
     */
    public function uploadVariantImages($variant, $id)
    {
        if (! isset($variant['images']['files'])) {
            return;
        }

        if ($images = $variant['images']['files']) {
            foreach ($images as $image) {
                if ($image instanceof UploadedFile) {                    
                    $this->create([
                        'path'       => $image->store('seller/variant/'.$id),
                        'marketplace_product_id' => $id,
                    ]);
                }
            }
        }
    }
}