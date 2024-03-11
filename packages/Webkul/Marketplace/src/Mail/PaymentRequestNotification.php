<?php

namespace Webkul\Marketplace\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * New Order Mail class
 *
 * @author    Jitendra Singh <jitendra@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PaymentRequestNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct( public $sellerOrder,public $admin )
    {
        $this->sellerName = $this->sellerOrder->seller->customer->first_name.' '.$this->sellerOrder->seller->customer->last_name;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to($this->admin->email,$this->admin->name)
            ->subject(trans('marketplace::app.mail.sales.paymentRequest.subject'))
            ->view('marketplace::shop.emails.payment.paymentRequest',['name'=> $this->admin->name,'sellerName'=> $this->sellerName]);
    }
}
