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
class AdminReportProductNotification extends Mailable
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
        public $seller, public $data
    ) {
        $this->sellerName = $this->seller->first_name.' '.$this->seller->last_name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to($this->data['admin']->email,$this->data['admin']->name)
                ->replyTo($this->data['email'], $this->data['name'])
                ->subject(trans('marketplace::app.shop.sellers.mails.report-product-toadmin.subject'))
                ->view('marketplace::shop.emails.product-flag-seller-admin', ['name' => $this->data['admin']->name]);
    }
}
