<?php

namespace Webkul\Marketplace\Helpers;

use Webkul\Product\Helpers\Indexers\Price\AbstractType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Webkul\Product\Models\Product;
use Webkul\Product\Models\ProductFlat;
use Webkul\Customer\Repositories\CustomerGroupRepository;
use Webkul\CatalogRule\Repositories\CatalogRuleProductPriceRepository;
use Webkul\CatalogRule\Helpers\CatalogRuleProductPrice;

class Price extends AbstractType
{
    /**
     * Create a new helper instance.
     *
     * @param  Webkul\Customer\Repositories\CustomerGroupRepository              $customerGroupRepository
     * @param  Webkul\CatalogRule\Repositories\CatalogRuleProductPriceRepository $catalogRuleProductPriceRepository
     * @param  Webkul\CatalogRule\Repositories\CatalogRuleProductPrice           $catalogRuleProductPriceHelper
     * @return void
     */
    public function __construct(
        protected CustomerGroupRepository $customerGroupRepository,
        protected CatalogRuleProductPriceRepository $catalogRuleProductPriceRepository,
        protected CatalogRuleProductPrice $catalogRuleProductPriceHelper
    ) {}

    /**
     * Get product minimal price.
     *
     * @param  integer  $qty
     * @return float
     */
    public function getMinimalPrice($qty = null)
    {
        static $price = [];

        if (array_key_exists($this->product->id, $price)) {
            return $price[$this->product->id];
        }

        if ($this->product->type == 'configurable') {
            return $price[$this->product->id] = $this->getVariantMinPrice($this->product);
        }

        if ($this->haveSpecialPrice($this->product)) {
            return $price[$this->product->id] = $this->product->special_price;
        }

        return $price[$this->product->id] = $this->product->price;
    }

    /**
     * Returns the product's minimal price
     *
     * @param Product $product
     * @return float
     */
    public function getVariantMinPrice($product)
    {
        static $price = [];

        $finalPrice = [];

        $productId = $product->id;

        if ($product instanceof ProductFlat) {
            $productId = $product->product_id;
            $productData = $product;
        } else {
            $productId = $product->product->id;
            $productData = $product->product;
        }

        $qb = ProductFlat::join('products', 'product_flat.product_id', '=', 'products.id')
            ->where('products.parent_id', $productId);

        $table_prefix = DB::getTablePrefix();

        $result = $qb
            ->distinct()
            ->selectRaw('IF( ' . $table_prefix . 'product_flat.special_price_from IS NOT NULL
                AND ' . $table_prefix . 'product_flat.special_price_to IS NOT NULL , IF( NOW( ) >= ' . $table_prefix . 'product_flat.special_price_from
                AND NOW( ) <= ' . $table_prefix . 'product_flat.special_price_to, IF( ' . $table_prefix . 'product_flat.special_price IS NULL OR ' . $table_prefix . 'product_flat.special_price = 0 , ' . $table_prefix . 'product_flat.price, LEAST( ' . $table_prefix . 'product_flat.special_price, ' . $table_prefix . 'product_flat.price ) ) , ' . $table_prefix . 'product_flat.price ) , IF( ' . $table_prefix . 'product_flat.special_price_from IS NULL , IF( ' . $table_prefix . 'product_flat.special_price_to IS NULL , IF( ' . $table_prefix . 'product_flat.special_price IS NULL OR ' . $table_prefix . 'product_flat.special_price = 0 , ' . $table_prefix . 'product_flat.price, LEAST( ' . $table_prefix . 'product_flat.special_price, ' . $table_prefix . 'product_flat.price ) ) , IF( NOW( ) <= ' . $table_prefix . 'product_flat.special_price_to, IF( ' . $table_prefix . 'product_flat.special_price IS NULL OR ' . $table_prefix . 'product_flat.special_price = 0 , ' . $table_prefix . 'product_flat.price, LEAST( ' . $table_prefix . 'product_flat.special_price, ' . $table_prefix . 'product_flat.price ) ) , ' . $table_prefix . 'product_flat.price ) ) , IF( ' . $table_prefix . 'product_flat.special_price_to IS NULL , IF( NOW( ) >= ' . $table_prefix . 'product_flat.special_price_from, IF( ' . $table_prefix . 'product_flat.special_price IS NULL OR ' . $table_prefix . 'product_flat.special_price = 0 , ' . $table_prefix . 'product_flat.price, LEAST( ' . $table_prefix . 'product_flat.special_price, ' . $table_prefix . 'product_flat.price ) ) , ' . $table_prefix . 'product_flat.price ) , ' . $table_prefix . 'product_flat.price ) ) ) AS final_price')
            ->where('product_flat.channel', core()->getCurrentChannelCode())
            ->where('product_flat.locale', app()->getLocale())
            ->get();

        foreach ($result as $price) {
            $finalPrice[] = $price->final_price;
        }

        $rulePrice =  null;

        if (request()->route()->getPrefix() != 'admin/catalog') {
            $rulePrice = $this->catalogRuleProductPriceRepository->scopeQuery(function ($query) use ($productData) {
                return $query->selectRaw('min(price) as price')
                    ->whereIn('product_id', $productData->variants()->pluck('id')->toArray())
                    ->where('channel_id', core()->getCurrentChannel()->id)
                    ->where('customer_group_id', $this->getCurrentCustomerGroupId())
                    ->where('rule_date', Carbon::now()->format('Y-m-d'));
            })->first();
        }

        if (
            empty($finalPrice) &&
            ! $rulePrice
        ) {
            return $price[$productId] = 0;
        }

        if (
            $rulePrice &&
            $rulePrice->price &&
            min($finalPrice) > $rulePrice->price
        ) {
            return $price[$productId] = $rulePrice->price;
        }

        return $price[$productId] = count($finalPrice) ? min($finalPrice) : 0;
    }

    /**
     * Returns the product's minimal price
     *
     * @param Product $product
     * @return float
     */
    public function getSpecialPrice($product)
    {
        static $price = [];

        if (array_key_exists($product->id, $price)) {
            return $price[$product->id];
        }

        if ($this->haveSpecialPrice($product)) {
            return $price[$product->id] = $product->special_price;
        }

        return $price[$product->id] = $product->price;
    }

    /**
     * @param Product $product
     * @return boolean
     */
    public function haveSpecialPrice($product)
    {
        if ($product instanceof ProductFlat) {
            $rulePrice = $this->catalogRuleProductPriceHelper->getRulePrice($product->product);
        } else {
            $rulePrice = $this->catalogRuleProductPriceHelper->getRulePrice($product);
        }

        if (
            (is_null($product->special_price) ||
            ! (float) $product->special_price) &&
            ! $rulePrice
        ) {
            return false;
        }

        if (! (float) $product->special_price) {
            if ($rulePrice) {
                $product->special_price = $rulePrice->price;

                return true;
            }
        } else {
            if (
                $rulePrice &&
                $rulePrice->price <= $product->special_price
            ) {
                $product->special_price = $rulePrice->price;

                return true;
            }

            if (core()->isChannelDateInInterval($product->special_price_from, $product->special_price_to)) {
                return true;
            }

            if ($rulePrice) {
                $this->product->special_price = $rulePrice->price;

                return true;
            }
        }

        return false;
    }

    /**
     * Returns current customer group id
     *
     * @return integer|null
     */
    public function getCurrentCustomerGroupId()
    {
        $guard = request()->has('token') ? 'api' : 'customer';

        if (auth()->guard($guard)->check()) {
            $customerGroupId = auth()->guard($guard)->user()->customer_group_id;
        } else {
            $customerGroupId = $this->customerGroupRepository->findOneByField('code', 'guest')->id;
        }

        return $customerGroupId;
    }
}
