<?php

namespace Webkul\Marketplace\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Container\Container as App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Webkul\Core\Eloquent\Repository;
use Webkul\Product\Repositories\ProductInventoryRepository;
use Webkul\Marketplace\Repositories\OrderItemRepository;

/**
 * Seller Reposotory
 *
 * @author    Jitendra Singh <jitendra@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class SellerRepository extends Repository
{
    public static $sellerDelete = null;

    /**
     * Create a new repository instance.
     *
     * @param  Webkul\Marketplace\Repositories\OrderItemRepository    $orderItemRepository
     * @param  Webkul\Product\Repositories\ProductInventoryRepository $productInventoryRepository
     * @param  \Illuminate\Container\Container                         $app
     * @return void
     */
    public function __construct(
        protected ProductInventoryRepository $productInventoryRepository,
        protected OrderItemRepository $orderItemRepository,
        App $app
    )
    {
        parent::__construct($app);
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Marketplace\Contracts\Seller';
    }

    /**
     * Retrive seller from url
     *
     * @param string $url
     * @return mixed
     */
    public function findByUrlOrFail($url, $columns = null)
    {
        if ($seller = $this->findOneByField('url', $url)) {
            return $seller;
        }

        throw (new ModelNotFoundException)->setModel(
            get_class($this->model), $url
        );
    }

    /**
     * @param array $data
     * @param $id
     * @param string $attribute
     * @return mixed
     */
    public function update(array $data, $id, $attribute = "id")
    {
        Event::dispatch('marketplace.seller.profile.update.before', $id);

        $seller = $this->find($id);

        parent::update($data, $id);

        if(isset($data['logo']))
            $this->uploadImages($data, $seller);

        if(isset($data['banner']))
            $this->uploadImages($data, $seller, 'banner');

        Event::dispatch('marketplace.seller.profile.update.after', $seller);

        return $seller;
    }

    /**
     * Checks if customer is registered as seller or not
     *
     * @param integer $customerId
     * @return boolean
     */
    public function isSeller($customerId)
    {
        $isSeller = $this->getModel()->where('customer_id', $customerId)
            ->limit(1)
            ->select(DB::raw(1))
            ->exists();

        return $isSeller;

        return $isSeller ? $this->isSellerApproved($customerId) : false;
    }

    /**
     * Checks if seller is approved or not
     *
     * @param $customerId
     * @return boolean
     */
    public function isSellerApproved($customerId)
    {
        $isSellerApproved = $this->getModel()->where('customer_id', $customerId)
            ->where('is_approved', 1)
            ->limit(1)
            ->select(DB::raw(1))
            ->exists();

        return $isSellerApproved ? true : false;
    }

    /**
     * @param array $data
     * @param mixed $seller
     * @return void
     */
    public function uploadImages($data, $seller, $type = "logo")
    {
        if (isset($data[$type])) {
            foreach ($data[$type] as $imageId => $image) {
                $file = $type . '.' . $imageId;
                $dir = 'seller/' . $seller->id;

                if (!request()->hasFile($file) && request()->hasFile($imageId)) {
                    $file = $imageId;
                }

                if (request()->hasFile($file)) {
                    if ($seller->{$type}) {
                        Storage::delete($seller->{$type});
                    }

                    $seller->{$type} = request()->file($file)->store($dir);
                    $seller->save();
                }
            }
        } else {
            if ($seller->{$type}) {
                Storage::delete($seller->{$type});
            }

            $seller->{$type} = null;
            $seller->save();
        }
    }

    /**
     * Returns top 4 popular sellers
     *
     * @return Collection
     */
    public function getPopularSellers()
    {
        $result = $this->getModel()
            ->leftJoin('marketplace_orders', 'marketplace_sellers.id', 'marketplace_orders.marketplace_seller_id')
            ->leftJoin('marketplace_order_items', 'marketplace_orders.id', 'marketplace_order_items.marketplace_order_id')
            ->leftJoin('order_items', 'marketplace_order_items.order_item_id', 'order_items.id')
            ->addSelect('marketplace_sellers.*')
            ->addSelect(DB::raw('SUM(qty_ordered) as total_qty_ordered'))
            ->groupBy('marketplace_sellers.id')
            ->where('marketplace_sellers.shop_title', '<>', NULL)
            ->where('marketplace_sellers.is_approved', 1)
            ->orderBy('total_qty_ordered', 'DESC')
            ->paginate(4);

        return $result;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        Event::dispatch('marketplace.seller.delete.before', $id);

        static::$sellerDelete = $this->find($id);

        parent::delete($id);

        Event::dispatch('marketplace.seller.delete.after', [$id, static::$sellerDelete]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteInventory($id)
    {
        $inventories = $this->productInventoryRepository->findWhere([
            'vendor_id' => $id
        ]);

        if (count($inventories)) {
            foreach ($inventories as $inventory) {
                if (isset ($inventory)) {
                    $this->productInventoryRepository->delete($inventory->id);
                }
            }
        }
    }

    /**
     * Returns seller's top selling products
     *
     * @return mixed
     */
    public function getTopSellingProducts($data = [], $sellerId = null)
    {
        $qb = $this->orderItemRepository->getModel()
            ->leftJoin('order_items', 'marketplace_order_items.order_item_id', 'order_items.id')
            ->leftJoin('marketplace_orders', 'marketplace_order_items.marketplace_order_id', 'marketplace_orders.id')
            ->leftJoin('marketplace_product_images', 'marketplace_order_items.marketplace_product_id', 'marketplace_product_images.marketplace_product_id')
            ->select(DB::raw('SUM(qty_ordered) as total_qty_ordered'))
            ->addSelect('order_items.id', 'product_id', 'product_type', 'name', 'marketplace_product_images.path')
            ->where('marketplace_orders.marketplace_seller_id', $sellerId)
            ->whereNull('order_items.parent_id')
            ->groupBy('product_id');

        if (! empty($data['startDate'])) {
            $qb->where('order_items.created_at', '>=', $data['startDate']);
        }

        if (! empty($data['endDate'])) {
            $qb->where('order_items.created_at', '<=', $data['endDate']);
        }

        return $qb->orderBy('total_qty_ordered', 'DESC')->limit(5)->get();
    }

    /**
     * Returns most sale customers
     *
     * @return mixed
     */
    public function getCustomerWithMostSales($data = [], $sellerId = null)
    {
        $table_prefix = DB::getTablePrefix();

        $qb = app(OrderRepository::class)->getModel()
            ->leftJoin('orders', 'marketplace_orders.order_id', 'orders.id')
            ->select(DB::raw('SUM('. $table_prefix .'marketplace_orders.base_grand_total) as total_base_grand_total'))
            ->addSelect(DB::raw('COUNT('. $table_prefix .'marketplace_orders.id) as total_orders'))
            ->addSelect('orders.id', 'orders.customer_id', 'orders.customer_email', DB::raw('CONCAT('. $table_prefix .'orders.customer_first_name, " ", '. $table_prefix .'orders.customer_last_name) as customer_full_name'))
            ->where('marketplace_orders.marketplace_seller_id', $sellerId)
            ->groupBy('orders.customer_email');

        if (! empty($data['startDate'])) {
            $qb->where('marketplace_orders.created_at', '>=', $data['startDate']);
        }

        if (! empty($data['endDate'])) {
            $qb->where('marketplace_orders.created_at', '<=', $data['endDate']);
        }

        return $qb->orderBy('total_base_grand_total', 'DESC')->limit(5)->get();
    }

    /**
     * Return stock threshold.
     *
     * @return mixed
     */
    public function getStockThreshold($sellerId = null)
    {
        return $this->productInventoryRepository->getModel()
            ->leftJoin('products', 'product_inventories.product_id', 'products.id')
            ->leftJoin('marketplace_products', 'products.id', 'marketplace_products.product_id')
            ->select(DB::raw('SUM(qty) as total_qty'))
            ->addSelect('product_inventories.product_id')
            ->where('products.type', '!=', 'configurable')
            ->where('marketplace_products.marketplace_seller_id', $sellerId)
            ->where('product_inventories.vendor_id', $sellerId)
            ->groupBy('product_id')
            ->orderBy('total_qty', 'ASC')
            ->limit(5)
            ->get();
    }

    /**
     * Returns array with total payout and remaining
     *
     * @return array
     */
    public function getSellerPayout ($data = [], $seller = null)
    {
        $statistics = [
            'total_payout' =>
                app(TransactionRepository::class)->scopeQuery(function($query) use ($seller) {
                    return $query->where('marketplace_transactions.marketplace_seller_id', $seller->id);
                })->sum('base_total'),

            'remaining_payout' =>
                app(OrderRepository::class)->scopeQuery(function ($query) use ($data, $seller) {
                    return $query->where('marketplace_orders.marketplace_seller_id', $seller->id)
                            ->where('status', 'completed')
                            ->whereIn('seller_payout_status', ['pending', 'requested'])
                            ->where('marketplace_orders.created_at', '>=', $data['startDate'])
                            ->where('marketplace_orders.created_at', '<=', $data['endDate']);
                })->sum('base_seller_total'),
        ];

        return $statistics;
    }

    public function getSellerInfo($sellerId)
    {
        return $this->getModel()->findOrFail($sellerId);
    }
}
