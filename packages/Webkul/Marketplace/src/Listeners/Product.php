<?php

namespace Webkul\Marketplace\Listeners;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Webkul\Core\Repositories\ChannelRepository;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Attribute\Repositories\AttributeOptionRepository;
use Webkul\Product\Repositories\ProductFlatRepository;
use Webkul\Product\Models\ProductAttributeValue;
use Webkul\Product\Models\ProductBundleOption;
use Webkul\Product\Repositories\ProductInventoryRepository;
use Webkul\Marketplace\Repositories\SellerRepository;
use Webkul\Marketplace\Models\GroupProductSellerPrice;
use Webkul\Marketplace\Repositories\OrderRepository;
use Webkul\Marketplace\Repositories\ProductRepository as MpProductRepository;

/**
 * Product event handler
 *
 * @author    Jitendra Singh <jitendra@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class Product
{
    /**
     * Attribute Object
     *
     * @var object
     */
    protected $attribute;

    /**
     * product_flat Object
     *
     * @var object
     */
    protected $flatColumns;

    /**
     * @var object
     */
    public $attributeTypeFields = [
        'text' => 'text',
        'textarea' => 'text',
        'price' => 'float',
        'boolean' => 'boolean',
        'select' => 'integer',
        'multiselect' => 'text',
        'datetime' => 'datetime',
        'date' => 'date',
        'file' => 'text',
        'image' => 'text',
        'checkbox' => 'text'
    ];

    /**
     * Attribute codes that can be fill during flat creation.
     *
     * @var string[]
     */
    protected $fillableAttributeCodes = [
        'sku',
        'name',
        'price',
        'weight',
        'status',
    ];

    /**
     * Create a new listener instance.
     *
     * @param  Webkul\Marketplace\Repositories\OrderRepository $order
     * @param  Webkul\Attribute\Repositories\AttributeRepository           $attributeRepository
     * @param  Webkul\Attribute\Repositories\AttributeOptionRepository     $attributeOptionRepository
     * @param  Webkul\Product\Repositories\ProductFlatRepository           $productFlatRepository
     * @param  Webkul\Marketplace\Repositories\MpProductRepository         $mpProductFlatRepository
     * @param  Webkul\Product\Repositories\ProductInventoryRepository      $productInventoryRepository
     * @param  Webkul\Marketplace\Repositories\SellerRepository            $sellerRepository
     * @return void
     */
    public function __construct(
        protected OrderRepository $order,
        protected AttributeRepository $attributeRepository,
        protected AttributeOptionRepository $attributeOptionRepository,
        protected ProductFlatRepository $productFlatRepository,
        protected MpProductRepository $mpProductRepository,
        protected ProductInventoryRepository $productInventoryRepository,
        protected SellerRepository $sellerRepository
    ) {
        $this->flatColumns = Schema::getColumnListing('product_flat');
    }

    public function BeforeProductCreate($id)
    {
        $data = request()->all();

        if (! isset($data['variants'])) {
            $sellerProduct = $this->mpProductRepository->findOneWhere([
                'product_id' => $id,
                'is_owner' => 1
            ]);

            if (isset($sellerProduct)) {
                $this->productInventoryRepository->saveInventories(array_merge($data, [
                    'vendor_id' => $sellerProduct->marketplace_seller_id
                ]), $sellerProduct->product);
            }
        } else if (isset($data['variants'])) {

            foreach ($data['variants'] as $productId => $variant) {

                $sellerProduct = $this->mpProductRepository->findOneWhere([
                    'product_id' => $productId,
                    'is_owner' => 1
                ]);

                if (isset($sellerProduct)) {
                    $this->productInventoryRepository->saveInventories(array_merge($variant, [
                        'vendor_id' => $sellerProduct->marketplace_seller_id
                    ]), $sellerProduct->product);
                }
            }
        }
    }

    /**
     * After the attribute is created
     *
     * @return void
     */
    public function afterAttributeCreatedUpdated($attribute)
    {
        if (! $attribute->is_user_defined) {
            return false;
        }

        if (! $attribute->use_in_flat) {
            $this->afterAttributeDeleted($attribute->id);
            return false;
        }

        if (! Schema::hasColumn('product_flat', $attribute->code)) {
            Schema::table('product_flat', function (Blueprint $table) use ($attribute) {
                $table->{$this->attributeTypeFields[$attribute->type]}($attribute->code)->nullable();

                if (
                    $attribute->type == 'select' ||
                    $attribute->type == 'multiselect'
                ) {
                    $table->string($attribute->code . '_label')->nullable();
                }
            });
        }
    }

    public function afterAttributeDeleted($attributeId)
    {
        $attribute = $this->attributeRepository->find($attributeId);

        if (Schema::hasColumn('product_flat', strtolower($attribute->code))) {
            Schema::table('product_flat', function (Blueprint $table) use ($attribute) {
                $table->dropColumn($attribute->code);

                if (
                    $attribute->type == 'select' ||
                    $attribute->type == 'multiselect'
                ) {
                    $table->dropColumn($attribute->code . '_label');
                }
            });
        }
    }

    /**
     * Creates product flat
     *
     * @param Product $product
     * @return void
     */
    public function afterProductCreatedUpdated($product)
    {
        $this->createFlat($product);

        if ($product->type == 'configurable') {
            foreach ($product->variants()->get() as $variant) {
                $this->createFlat($variant, $product);
            }
        }

        if (
            $product->type == 'grouped' &&
            ! empty(request()->links)
        ) {
            $linksProducts = request()->links;
            if (count($linksProducts) > 0) {

                //remove all record related product.
                DB::table('product_grouped_products')->where('product_id', $product->id)->delete();
                foreach ($linksProducts as $linkProduct) {
                    DB::table('product_grouped_products')->insert([
                        'product_id' => $product->id,
                        'qty' => $linkProduct['qty'],
                        'associated_product_id' => $linkProduct['associated_product_id'],
                        'sort_order' => $linkProduct['sort_order']
                    ]);
                }
            }
        }

        if (
            $product != NULL &&
            $product->type == 'bundle' &&
            ! empty(request()->bundle_item)
        ) {
            $productId = $product->id;
            DB::transaction(function () use ($productId) {

                //remove existing record.
                ProductBundleOption::where('product_id', $productId)->delete();
                foreach (request()->bundle_item as $bundleItem) {

                    //create new record
                    $response = ProductBundleOption::create(['type' => $bundleItem['input_type'], 'is_required' => ($bundleItem['is_required'] == "YES") ? 1 : 0, 'sort_order' => $bundleItem['sort_order'], 'product_id' => $productId]);

                    if ($response != NULL) {

                        //remove existing translation.
                        DB::table('product_bundle_option_translations')
                            ->where('product_bundle_option_id', $response->id)
                            ->delete();

                        //insert new record.
                        DB::table('product_bundle_option_translations')
                            ->insert(['locale' => core()->getRequestedLocaleCode(), 'label' => $bundleItem['option_title'], 'product_bundle_option_id' => $response->id,]);

                        //remove existing translation.
                        DB::table('product_bundle_option_products')->where('product_bundle_option_id', $response->id)->delete();

                        /**
                         * Save bundle product save option.
                         */
                        if (! empty($bundleItem['bundle_product_option'])) {
                            $bundleProductOptions = $bundleItem['bundle_product_option'];
                            if (count($bundleProductOptions) > 0) {
                                $productOptions = [];
                                foreach ($bundleProductOptions as $bundleProductOption) {

                                    $productOptions[] = ['qty' => $bundleProductOption['quantity'], 'is_user_defined' => 1, 'is_default' => array_key_exists('is_default', $bundleProductOption) ? 1 : 0, 'sort_order' => $bundleProductOption['sort_order'], 'product_bundle_option_id' => $response->id, 'product_id' => $bundleProductOption['associated_product_id']];
                                }

                                DB::table('product_bundle_option_products')->insert($productOptions);
                            }
                        }
                    }
                }
            });
        }
    }

    /**
     * Creates product flat
     *
     * @param Product $product
     * @param Product $parentProduct
     * @return void
     */
    public function createFlat($product, $parentProduct = null)
    {
        static $familyAttributes = [];

        static $superAttributes = [];

        if (! array_key_exists($product->attribute_family_id, $familyAttributes)) {
            $familyAttributes[$product->attribute_family_id] = $product->attribute_family->custom_attributes;
        }

        if (
            $parentProduct
            && ! array_key_exists($parentProduct->id, $superAttributes)
        ) {
            $superAttributes[$parentProduct->id] = $parentProduct->super_attributes()->pluck('code')->toArray();
        }

        if (isset($product['channels'])) {
            foreach ($product['channels'] as $channel) {
                $channels[] = $this->getChannel($channel)->code;
            }
        } elseif (isset($parentProduct['channels'])) {
            foreach ($parentProduct['channels'] as $channel) {
                $channels[] = $this->getChannel($channel)->code;
            }
        } else {
            $channels[] = core()->getDefaultChannelCode();
        }

        $attributeValues = $product->attribute_values()->get();

        foreach (core()->getAllChannels() as $channel) {
            if (in_array($channel->code, $channels)) {
                foreach ($channel->locales as $locale) {
                    $productFlat = $this->productFlatRepository->updateOrCreate([
                        'product_id' => $product->id,
                        'channel'    => $channel->code,
                        'locale'     => $locale->code,
                    ]);

                    foreach ($familyAttributes[$product->attribute_family_id] as $attribute) {
                        if (
                            ($parentProduct
                                && !in_array($attribute->code, array_merge($superAttributes[$parentProduct->id], $this->fillableAttributeCodes))
                            )
                            || in_array($attribute->code, ['tax_category_id'])
                            || !in_array($attribute->code, $this->flatColumns)
                        ) {
                            continue;
                        }

                        if ($attribute->value_per_channel) {
                            if ($attribute->value_per_locale) {
                                $productAttributeValue = $attributeValues
                                    ->where('channel', $channel->code)
                                    ->where('locale', $locale->code)
                                    ->where('attribute_id', $attribute->id)
                                    ->first();
                            } else {
                                $productAttributeValue = $attributeValues
                                    ->where('channel', $channel->code)
                                    ->where('attribute_id', $attribute->id)
                                    ->first();
                            }
                        } else {
                            if ($attribute->value_per_locale) {
                                $productAttributeValue = $attributeValues
                                    ->where('locale', $locale->code)
                                    ->where('attribute_id', $attribute->id)
                                    ->first();
                            } else {
                                $productAttributeValue = $attributeValues
                                    ->where('attribute_id', $attribute->id)
                                    ->first();
                            }
                        }

                        $productFlat->{$attribute->code} = $productAttributeValue[ProductAttributeValue::$attributeTypeFields[$attribute->type]] ?? null;

                        if ($attribute->type == 'select') {
                            $attributeOption = $this->getAttributeOptions($productFlat->{$attribute->code});

                            if ($attributeOption) {
                                if ($attributeOptionTranslation = $attributeOption->translate($locale->code)) {
                                    $productFlat->{$attribute->code . '_label'} = $attributeOptionTranslation->label;
                                } else {
                                    $productFlat->{$attribute->code . '_label'} = $attributeOption->admin_name;
                                }
                            }
                        } elseif ($attribute->type == 'multiselect') {
                            $attributeOptionIds = explode(',', $productFlat->{$attribute->code});

                            if (count($attributeOptionIds)) {
                                $attributeOptions = $this->getAttributeOptions($productFlat->{$attribute->code});

                                $optionLabels = [];

                                foreach ($attributeOptions as $attributeOption) {
                                    if ($attributeOptionTranslation = $attributeOption->translate($locale->code)) {
                                        $optionLabels[] = $attributeOptionTranslation->label;
                                    } else {
                                        $optionLabels[] = $attributeOption->admin_name;
                                    }
                                }

                                $productFlat->{$attribute->code . '_label'} = implode(', ', $optionLabels);
                            }
                        }
                    }

                    $productFlat->min_price = $product->getTypeInstance()->getMinimalPrice();

                    $productFlat->max_price = $product->getTypeInstance()->getMaximumPrice();

                    if ($parentProduct) {
                        $parentProductFlat = $this->productFlatRepository->findOneWhere([
                            'product_id' => $parentProduct->id,
                            'channel'    => $channel->code,
                            'locale'     => $locale->code,
                        ]);

                        if ($parentProductFlat) {
                            $productFlat->parent_id = $parentProductFlat->id;
                        }
                    }

                    $productFlat->save();
                }
            } else {
                $route = request()->route() ? request()->route()->getName() : "";

                if ($route == 'admin.catalog.products.update') {
                    $productFlat = $this->productFlatRepository->findOneWhere([
                        'product_id' => $product->id,
                        'channel'    => $channel->code,
                    ]);

                    if ($productFlat) {
                        $this->productFlatRepository->delete($productFlat->id);
                    }
                }
            }
        }
    }

    public function saveProductDetails($product, $data)
    {

        if ($product != NULL) {
            $productDetail = DB::table("products")->where('id', $product->product_id)->first();
            if (
                $productDetail !=  NULL &&
                $productDetail->type == "grouped"
            ) {
                $seller = $this->sellerRepository->findOneByField('customer_id', auth()->guard('customer')->user()->id);
                if (
                    $seller &&
                    ! empty(request()->links)
                ) {
                    if (count(request()->links) > 0) {
                        foreach (request()->links as $link) {
                            GroupProductSellerPrice::updateOrCreate(
                                ['product_grouped_product_id' => $link['product_grouped_product_id']],
                                [
                                    'product_grouped_product_id' => $link['product_grouped_product_id'],
                                    'marketplace_seller_id' => $seller->id,
                                    'seller_sell_price' => $link['seller_sell_price']
                                ]
                            );
                        }
                    }
                }
            }
        }

        if (
            $product != NULL &&
            $product->product->type == 'bundle'
        ) {
            // if( $product != NULL && $product->product->type == 'bundle' && $data['bundle_options']) {
            return false;
            $productId = $product->product->id;

            DB::transaction(function () use ($productId, $data) {

                //remove existing record.
                ProductBundleOption::where('product_id', $productId)->delete();
                foreach ($data['bundle_options'] as $bundleItem) {

                    //create new record
                    $response = ProductBundleOption::create(['type' => $bundleItem['type'], 'is_required' => ($bundleItem['is_required'] == 1) ? 1 : 0, 'sort_order' => $bundleItem['sort_order'], 'product_id' => $productId]);

                    if ($response != NULL) {

                        //remove existing translation.
                        DB::table('product_bundle_option_translations')
                            ->where('product_bundle_option_id', $response->id)
                            ->delete();

                        //insert new record.
                        DB::table('product_bundle_option_translations')
                            ->insert(['locale' => core()->getRequestedLocaleCode(), 'label' => $bundleItem['en']['label'], 'product_bundle_option_id' => $response->id,]);

                        //remove existing translation.
                        DB::table('product_bundle_option_products')->where('product_bundle_option_id', $response->id)->delete();

                        /**
                         * Save bundle product save option.
                         */
                        if (!empty($bundleItem['products'])) {
                            $bundleProductOptions = $bundleItem['products'];
                            if (count($bundleProductOptions) > 0) {
                                $productOptions = [];
                                foreach ($bundleProductOptions as $bundleProductOption) {

                                    $productOptions[] = ['qty' => $bundleProductOption['qty'], 'is_user_defined' => 1, 'is_default' => array_key_exists('is_default', $bundleProductOption) ? 1 : 0, 'sort_order' => $bundleProductOption['sort_order'], 'product_bundle_option_id' => $response->id, 'product_id' => $bundleProductOption['product_id']];
                                }

                                DB::table('product_bundle_option_products')->insert($productOptions);
                            }
                        }
                    }
                }
            });
        }

        if ($product != NULL && $product->product->type == 'booking') {
        }
    }

    /**
     * Update product for seller if Seller is owner
     *
     */
    public function afterAdminProductUpdate($product)
    {
        if (
            request()->get('status') == 1
            || (
                request()['mass-action-type'] == 'update'
                && request()['update-options'] == 1
            )
        ) {
            $sellerProduct = $this->mpProductRepository->findOneWhere([
                'product_id' => $product->id,
                'is_owner' => 1
            ]);

            if ($sellerProduct) {
                $this->mpProductRepository->where('product_id', $product->id)->update(['is_approved' => 1]);
            }
        }
    }

    /**
     * @param  string  $id
     * @return mixed
     */
    public function getChannel($id)
    {
        static $channels = [];

        if (isset($channels[$id])) {
            return $channels[$id];
        }

        return $channels[$id] = app(ChannelRepository::class)->findOrFail($id);
    }

    /**
     * @param  string  $value
     * @return mixed
     */
    public function getAttributeOptions($value)
    {
        if (! $value) {
            return;
        }

        static $attributeOptions = [];

        if (array_key_exists($value, $attributeOptions)) {
            return $attributeOptions[$value];
        }

        if (is_numeric($value)) {
            return $attributeOptions[$value] = $this->attributeOptionRepository->find($value);
        } else {
            $attributeOptionIds = explode(',', $value);

            return $attributeOptions[$value] = $this->attributeOptionRepository->findWhereIn('id', $attributeOptionIds);
        }
    }
}
