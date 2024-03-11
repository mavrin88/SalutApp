<?php

namespace Webkul\Marketplace\Http\Controllers\Shop\Account\Sales;

use Illuminate\Http\Request;
use Webkul\Marketplace\Http\Controllers\Shop\Controller;
use Webkul\Marketplace\Repositories\OrderRepository;
use Webkul\Marketplace\Repositories\SellerRepository;
use Webkul\Sales\Repositories\OrderItemRepository as BaseOrderItem;
use Webkul\Sales\Repositories\ShipmentRepository;

/**
 * Shipment controller
 *
 * @author Anmol Singh Chauhan <anmol.chauhan207@webkul.in>
 * @copyright 2022 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class ShipmentController extends Controller
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
     * @param  Webkul\Marketplace\Repositories\OrderRepository    $order
     * @param  Webkul\Marketplace\Repositories\SellerRepository   $seller
     * @param  Webkul\Sales\Repositories\OrderItemRepository      $baseOrderItem
     * @param  Webkul\Marketplace\Repositories\ShipmentRepository $shipment
     * @return void
     */
    public function __construct(
        protected OrderRepository $order,
        protected SellerRepository $seller,
        protected BaseOrderItem $baseOrderItem,
        protected ShipmentRepository $shipment
    ) {
        $this->_config = request('_config');
    }

    /**
     * Show the view for the specified resource.
     *
     * @param  int  $orderId
     * @return \Illuminate\Http\Response
     */
    public function create($orderId)
    {
        if (! core()->getConfigData('marketplace.settings.general.can_create_shipment'))
            return redirect()->back();

        $seller = $this->seller->findOneWhere([
            'customer_id' => auth()->guard('customer')->user()->id
        ]);

        $sellerOrder = $this->order->findOneWhere([
            'order_id' => $orderId,
            'marketplace_seller_id' => $seller->id
        ]);

        return view($this->_config['view'], compact('sellerOrder'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param int $orderId
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $orderId)
    {
        $seller = $this->seller->findOneWhere([
            'customer_id' => auth()->guard('customer')->user()->id
        ]);

        $sellerOrder = $this->order->findOneWhere([
            'order_id' => $orderId,
            'marketplace_seller_id' => $seller->id
        ]);

        if (! $sellerOrder->canShip()) {
            session()->flash('error', 'Order shipment creation is not allowed.');

            return redirect()->back();
        }

        $this->validate(request(), [
            'shipment.carrier_title' => 'required',
            'shipment.track_number' => 'required',
            'shipment.source' => 'required',
            'shipment.items.*.*' => 'required|numeric|min:0',
        ]);

        $data = array_merge(request()->all(), [
            'vendor_id' => $sellerOrder->marketplace_seller_id
        ]);

        if (! $this->isInventoryValidate($data)) {
            session()->flash('error', 'Requested quantity is invalid or not available.');

            return redirect()->back();
        }

        $this->shipment->create(array_merge($data, [
            'order_id' => $orderId
        ]));

        session()->flash('success', 'Shipment created successfully.');

        return redirect()->route($this->_config['redirect'], $orderId);
    }

    /**
     * Checks if requested quantity available or not
     *
     * @param array $data
     * @return boolean
     */
    public function isInventoryValidate(&$data)
    {
        if (! isset($data['shipment']['items'])) {
            return;
        }

        $valid = false;

        $inventorySourceId = $data['shipment']['source'];

        foreach ($data['shipment']['items'] as $itemId => $inventorySource) {
            $qty = $inventorySource[$inventorySourceId];

            if ((int) $qty) {
                $orderItem = $this->baseOrderItem->find($itemId);

                if ($orderItem->qty_to_ship < $qty) {
                    return false;
                }

                if ($orderItem->getTypeInstance()->isComposite()) {
                    foreach ($orderItem->children as $child) {
                        if (!$child->qty_ordered) {
                            continue;
                        }

                        $finalQty = ($child->qty_ordered / $orderItem->qty_ordered) * $qty;

                        $availableQty = $child->product->inventories()
                            ->where('inventory_source_id', $inventorySourceId)
                            ->where('vendor_id', $data['vendor_id'])
                            ->sum('qty');

                        if (
                            $child->qty_to_ship < $finalQty
                            || $availableQty < $finalQty
                        ) {
                            return false;
                        }
                    }
                } else {
                    $availableQty = $orderItem->product->inventories()
                        ->where('inventory_source_id', $inventorySourceId)
                        ->where('vendor_id', $data['vendor_id'])
                        ->sum('qty');

                    if (
                        $orderItem->qty_to_ship < $qty
                        || $availableQty < $qty
                    ) {
                        return false;
                    }
                }

                $valid = true;
            } else {
                unset($data['shipment']['items'][$itemId]);
            }
        }

        return $valid;
    }
}
