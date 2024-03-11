<?php

namespace Webkul\Marketplace\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Contact Seller Mail class
 *
 * @author    Jitendra Singh <jitendra@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class AdminReportSellerNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param Seller $seller
     * @param array  $data
     * @return void
     */
    public function __construct(
        public $seller,
        public $data
    ) {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(core()->getSenderEmailDetails()['email'], core()->getSenderEmailDetails()['name'])
            ->to(core()->getAdminEmailDetails()['email'], __('admin::app.catalog.attributes.admin'))
            ->replyTo($this->data['email'], $this->data['name'])
            ->subject(trans('marketplace::app.shop.sellers.mails.contact-seller.subject'))
            ->view('marketplace::shop.velocity.emails.contact-seller', [
                'mail_to' => 'admin',
                'seller_name' => $this->seller->customer->name,
                'query' => $this->data['query'],
                'customer_name' => $this->data['name']
            ]);
    }
}
