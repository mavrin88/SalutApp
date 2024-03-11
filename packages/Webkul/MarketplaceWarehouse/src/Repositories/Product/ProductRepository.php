<?php

namespace Webkul\MarketplaceWarehouse\Repositories\Product;

use Webkul\Product\Repositories\ProductRepository as CoreProductRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository extends CoreProductRepository
{
    /**
     * Search product from database.
     *
     * @param  string  $categoryId
     * @return \Illuminate\Support\Collection
     */
    public function searchFromDatabase($categoryId)
    {
        $params = array_merge([
            'status'               => 1,
            'visible_individually' => 1,
            'url_key'              => null,
        ], request()->input());

        if (! empty($params['search'])) {
            $params['name'] = $params['search'];
        }

        $status = core()->getConfigData('marketplace.settings.general.status');

        if ($status) {
            $adminAllowCity = core()->getConfigData('sales.shipping.origin.city');

            $city = str_replace(' ', '+', session()->get('location'));

            $isAdminAllowCity = ! empty($city) && (strtolower($city) == strtolower($adminAllowCity));

            $sellers = app('Webkul\MarketplaceWarehouse\Helpers\Location')->getAllowedSellers();

            $isSellerAllowCity = empty($sellers) ? [] : array_column($sellers->toArray(), 'id');

            $productIds = [];

            $sellerProductIds = [];

            if ($isAdminAllowCity) {
                $productIds = app('Webkul\MarketplaceWarehouse\Repositories\MpProduct\ProductRepository')->getSerachProductIds($isSellerAllowCity);
            } else {
                $sellerProductIds = app('Webkul\MarketplaceWarehouse\Repositories\MpProduct\ProductRepository')->getSerachProductIds($isSellerAllowCity, true);
            }
        }

        $query = $this->with([
            'images',
            'videos',
            'attribute_values',
            'price_indices',
            'inventory_indices',
            'reviews',
        ])->scopeQuery(function ($query) use ($params, $categoryId, $isAdminAllowCity, $productIds ,$sellerProductIds) {
            $prefix = DB::getTablePrefix();

            $qb = $query->distinct()
                ->select('products.*')
                ->leftJoin('products as variants', DB::raw('COALESCE(' . $prefix . 'variants.parent_id, ' . $prefix . 'variants.id)'), '=', 'products.id')
                ->leftJoin('product_price_indices', function ($join) {
                    $customerGroup = $this->customerRepository->getCurrentGroup();

                    $join->on('products.id', '=', 'product_price_indices.product_id')
                        ->where('product_price_indices.customer_group_id', $customerGroup->id);
                });

            if ($isAdminAllowCity) {
                $query->whereNotIn('products.id', $productIds);
            } else {
                $query->whereIn('products.id', $sellerProductIds);
            }

            if ($categoryId) {
                $qb->leftJoin('product_categories', 'product_categories.product_id', '=', 'products.id')
                    ->whereIn('product_categories.category_id', explode(',', $categoryId));
            }

            if (! empty($params['type'])) {
                $qb->where('products.type', $params['type']);
            }

            /**
             * Filter query by price.
             */
            if (! empty($params['price'])) {
                $priceRange = explode(',', $params['price']);

                $qb->whereBetween('product_price_indices.min_price', [
                    core()->convertToBasePrice(current($priceRange)),
                    core()->convertToBasePrice(end($priceRange)),
                ]);
            }

            /**
             * Retrieve all the filterable attributes.
             */
            $filterableAttributes = $this->attributeRepository->getProductDefaultAttributes(array_keys($params));

            /**
             * Filter the required attributes.
             */
            $attributes = $filterableAttributes->whereIn('code', [
                'name',
                'status',
                'visible_individually',
                'url_key',
            ]);

            /**
             * Filter collection by required attributes.
             */
            foreach ($attributes as $attribute) {
                $alias = $attribute->code . '_product_attribute_values';

                $qb->leftJoin('product_attribute_values as ' . $alias, 'products.id', '=', $alias . '.product_id')
                    ->where($alias . '.attribute_id', $attribute->id);

                if ($attribute->code == 'name') {
                    $qb->where($alias . '.text_value', 'like', '%' . urldecode($params['name']) . '%');
                } elseif ($attribute->code == 'url_key') {
                    if (empty($params['url_key'])) {
                        $qb->whereNotNull($alias . '.text_value');
                    } else {
                        $qb->where($alias . '.text_value', 'like', '%' . urldecode($params['url_key']) . '%');
                    }
                } else {
                    $qb->where($alias . '.' . $attribute->column_name, 1);
                }
            }

            /**
             * Filter the filterable attributes.
             */
            $attributes = $filterableAttributes->whereNotIn('code', [
                'price',
                'name',
                'status',
                'visible_individually',
                'url_key',
            ]);

            /**
             * Filter query by attributes.
             */
            if ($attributes->isNotEmpty()) {
                $qb->leftJoin('product_attribute_values', 'products.id', '=', 'product_attribute_values.product_id');

                $qb->where(function ($filterQuery) use ($params, $attributes) {
                    foreach ($attributes as $attribute) {
                        $filterQuery->orWhere(function ($attributeQuery) use ($params, $attribute) {
                            $attributeQuery = $attributeQuery->where('product_attribute_values.attribute_id', $attribute->id);

                            $values = explode(',', $params[$attribute->code]);

                            if ($attribute->type == 'price') {
                                $attributeQuery->whereBetween('product_attribute_values.' . $attribute->column_name, [
                                    core()->convertToBasePrice(current($values)),
                                    core()->convertToBasePrice(end($values)),
                                ]);
                            } else {
                                $attributeQuery->whereIn('product_attribute_values.' . $attribute->column_name, $values);
                            }
                        });
                    }
                });

                /**
                 * This is key! if a product has been filtered down to the same number of attributes that we filtered on,
                 * we know that it has matched all of the requested filters.
                 *
                 * To Do (@devansh): Need to monitor this.
                 */
                $qb->groupBy('products.id');
                $qb->havingRaw('COUNT(*) = ' . count($attributes));
            }

            /**
             * Sort collection.
             */
            $sortOptions = $this->getSortOptions($params);

            if ($sortOptions['order'] != 'rand') {
                $attribute = $this->attributeRepository->findOneByField('code', $sortOptions['sort']);

                if ($attribute) {
                    if ($attribute->code === 'price') {
                        $qb->orderBy('product_price_indices.min_price', $sortOptions['order']);
                    } else {
                        $alias = 'sort_product_attribute_values';

                        $qb->leftJoin('product_attribute_values as ' . $alias, function ($join) use ($alias, $attribute) {
                            $join->on('products.id', '=', $alias . '.product_id')
                                ->where($alias . '.attribute_id', $attribute->id)
                                ->where($alias . '.channel', core()->getRequestedChannelCode())
                                ->where($alias . '.locale', core()->getRequestedLocaleCode());
                        })
                            ->orderBy($alias . '.' . $attribute->column_name, $sortOptions['order']);
                    }
                } else {
                    /* `created_at` is not an attribute so it will be in else case */
                    $qb->orderBy('products.created_at', $sortOptions['order']);
                }
            } else {
                return $qb->inRandomOrder();
            }

            return $qb->groupBy('products.id');
        });

        /**
         * Apply scope query so we can fetch the raw sql and perform a count.
         */
        $query->applyScope();

        $countQuery = clone $query->model;

        $count = collect(
            DB::select("select count(id) as aggregate from ({$countQuery->select('products.id')->reorder('products.id')->toSql()}) c",
            $countQuery->getBindings())
        )->pluck('aggregate')->first();

        $items = [];

        $limit = $this->getPerPageLimit($params);

        $currentPage = Paginator::resolveCurrentPage('page');

        if ($count > 0) {
            $query->scopeQuery(function ($query) use ($currentPage, $limit) {
                return $query->forPage($currentPage, $limit);
            });

            $items = $query->get();
        }

        return new LengthAwarePaginator($items, $count, $limit, $currentPage, [
            'path'  => request()->url(),
            'query' => request()->query(),
        ]);
    }
}
