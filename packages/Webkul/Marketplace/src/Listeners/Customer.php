<?php

namespace Webkul\Marketplace\Listeners;

use Illuminate\Support\Facades\Mail;
use Webkul\Marketplace\Repositories\SellerRepository;
use Webkul\User\Repositories\AdminRepository;
use Webkul\Marketplace\Repositories\SellerCategoryRepository;
use Webkul\Marketplace\Mail\SellerWelcomeNotification;
use Webkul\Marketplace\Mail\SellerApprovalNotification;
use Webkul\Marketplace\Mail\SellerUpdateNotification;
use Webkul\Marketplace\Mail\NewSellerNotification;
use Webkul\Marketplace\Mail\SellerDeleteNotification;

/**
 * Customer event handler
 *
 * @author    Jitendra Singh <jitendra@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class Customer
{
    /**
     * Create a new customer event listener instance.
     *
     * @param  Webkul\Marketplace\Repositories\SellerRepository $seller
     * @param  Webkul\User\Repositories\AdminRepository  $admin
     * @param  Webkul\Marketplace\Repositories\SellerCategoryRepository $sellerCategoryRepository;
     * @return void
     */
    public function __construct(
        protected SellerRepository $seller,
        protected AdminRepository $admin,
        protected SellerCategoryRepository $sellerCategoryRepository
    ) {}

    /**
     * Register seller if customer requested
     *
     * @param mixed $customer
     */
    public function registerSeller($customer)
    {
        $admin = $this->admin->findOneWhere(['role_id' => 1]);

        if ($url = request()->input('url')) {
            $seller = $this->seller->findOneByField([
                'url' => $url
            ]);

            if (! $seller) {
                $data = [
                    'customer_id' => $customer->id,
                    'url' => $url,
                    'is_approved' => core()->getConfigData('marketplace.settings.general.seller_approval_required') ? 0 : 1
                ];

                $seller = $this->seller->create($data);

                try {
                    if ($seller->is_approved) {
                        Mail::send(new SellerApprovalNotification($seller));
                    } else {
                        Mail::send(new SellerWelcomeNotification($seller));

                        Mail::send(new NewSellerNotification($seller, $admin));
                    }
                } catch (\Exception $e) {
                }
            }
        }
    }

    /**
     * Delete inventory of seller after delete
     *
     * @param mixed $id
     */
    public function afterSellerDelete($id, $seller)
    {
        $this->seller->deleteInventory($id);
        $sellerCategories = $this->sellerCategoryRepository->findOneByField('seller_id', $id);
        if ($sellerCategories) {
            $sellerCategories->delete();
        }

        try {
            Mail::send(new SellerDeleteNotification($seller));
        } catch (\Exception $e) {
            report($e);
        }
    }

    public function afterSellerUpdate($seller)
    {
        try {
            Mail::send(new SellerUpdateNotification($seller));
        } catch (\Exception $e) {
            report($e);
        }
    }
}
