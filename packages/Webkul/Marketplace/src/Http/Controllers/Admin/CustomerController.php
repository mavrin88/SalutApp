<?php

namespace Webkul\Marketplace\Http\Controllers\Admin;

use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Marketplace\Repositories\SellerRepository;
use Illuminate\Support\Facades\Event;
use Webkul\Admin\Mail\NewCustomerNotification;
use Illuminate\Support\Facades\Mail;

/**
 * Customer controlller
 *
 * @author Anmol Singh Chauhan <anmol.chauhan207@webkul.in>
 * @copyright 2022 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class CustomerController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * Create a new controller instance.
     *
     * @param \Webkul\Customer\Repositories\CustomerRepository  $customerRepository
     * @param \Webkul\Marketplace\Repositories\SellerRepository $sellerRepository
     */
    public function __construct(
        protected CustomerRepository $customerRepository,
        protected SellerRepository $sellerRepository
    ) {
        $this->_config = request('_config');

        $this->middleware('admin');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'first_name'    => 'string|required',
            'last_name'     => 'string|required',
            'gender'        => 'required',
            'email'         => 'required|unique:customers,email',
            'date_of_birth' => 'date|before:today',
        ]);

        $data = request()->all();

        $password = rand(100000, 10000000);

        $data['password'] = bcrypt($password);

        $data['is_verified'] = 1;

        Event::dispatch('customer.registration.before');

        $customer = $this->customerRepository->create($data);

        Event::dispatch('customer.registration.after', $customer);

        try {
            $configKey = 'emails.general.notifications.emails.general.notifications.customer';
            if (core()->getConfigData($configKey)) {
                Mail::queue(new NewCustomerNotification($customer, $password));
            }
        } catch (\Exception $e) {
            report($e);
        }

        session()->flash('success', trans('admin::app.response.create-success', ['name' => 'Seller']));

        return redirect()->route($this->_config['redirect']);
    }
}
