<?php

namespace Webkul\Marketplace\Http\Controllers\Admin;

use Illuminate\Http\Response;
use Webkul\Marketplace\Repositories\ReviewRepository;
use Webkul\Marketplace\DataGrids\Admin\ReviewDataGrid;

/**
 * Marketplace review controller
 *
 * @author Anmol Singh Chauhan <anmol.chauhan207@webkul.in>
 * @copyright 2022 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class ReviewController extends Controller
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
     * @param  Webkul\Marketplace\Repositories\ReviewRepository $reviewRepository
     * @return void
     */
    public function __construct(
        protected ReviewRepository $reviewRepository
    ) {
        $this->_config = request('_config');
    }

    /**
     * Method to populate the seller review page which will be populated.
     *
     * @return Mixed
     */
    public function index($url)
    {
        if (request()->ajax()) {
            return app(ReviewDataGrid::class)->toJson();
        }

        return view($this->_config['view']);
    }

    /**
     * Mass updates the products
     *
     * @return response
     */
    public function massUpdate()
    {
        $data = request()->all();

        if (
            ! isset($data['mass-action-type']) ||
            ! $data['mass-action-type'] == 'update'
        ) {
            return redirect()->back();
        }

        $reviewIds = explode(',', $data['indexes']);

        foreach ($reviewIds as $reviewId) {
            $this->reviewRepository->update([
                'status' => $data['update-options']
            ], $reviewId);
        }

        session()->flash('success', trans('marketplace::app.admin.reviews.mass-update-success'));

        return redirect()->route($this->_config['redirect']);
    }
}
