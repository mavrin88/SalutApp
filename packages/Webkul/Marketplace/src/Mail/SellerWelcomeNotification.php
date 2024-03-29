<?php

namespace Webkul\Marketplace\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Sellee Welcome Mail class
 *
 * @author    Jitendra Singh <jitendra@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class SellerWelcomeNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        public $seller
    ) {

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to($this->seller->customer->email, $this->seller->customer->name)
                ->subject(trans('marketplace::app.mail.seller.welcome.subject'))
                ->view('marketplace::shop.emails.seller.welcome');
    }
}
