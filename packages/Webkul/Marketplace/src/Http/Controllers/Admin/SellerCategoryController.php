<?php

namespace Webkul\Marketplace\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use Webkul\Category\Repositories\CategoryRepository;
use Webkul\Marketplace\Repositories\SellerRepository;
use Webkul\Marketplace\Repositories\SellerCategoryRepository;
use Webkul\Marketplace\DataGrids\Admin\SellerCategoryDataGrid;

class SellerCategoryController extends Controller
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
        protected SellerRepository $sellerRepository,
        protected CategoryRepository $categoryRepository,
        protected SellerCategoryRepository $sellerCategoryRepository
    ) {
        $this->_config = request('_config');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(SellerCategoryDataGrid::class)->toJson();
        }

        return view($this->_config['view']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sellers = $this->sellerRepository->all()->where('is_approved', 1);

        $categories = $this->categoryRepository->getCategoryTree();

        return view($this->_config['view'], compact('sellers', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = request()->all();

        if (! isset($data['categories'])) {
            session()->flash('error', __('marketplace::app.admin.sellers.category.save-error'));

            return redirect()->route('admin.marketplace.seller.category.index');
        }

        $data['categories'] = json_encode($data['categories']);

        $sellerCategories = $this->sellerCategoryRepository->findOneByField('seller_id', $data['seller_id']);

        if ($sellerCategories) {
            $a1 = json_decode($data['categories']);
            $a2 = json_decode($sellerCategories->categories);
            $data['categories'] = json_encode(array_unique(array_merge($a1, $a2), SORT_REGULAR));

            $sellerCategories->update($data);

            session()->flash('success', __('marketplace::app.admin.sellers.category.update-success'));
        } else {
            $this->sellerCategoryRepository->create($data);

            session()->flash('success', __('marketplace::app.admin.sellers.category.save-success'));
        }
        
        return redirect()->route('admin.marketplace.seller.category.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $sellerCategories = $this->sellerCategoryRepository->find($id);

        $sellers = $this->sellerRepository->all();

        $categories = $this->categoryRepository->getCategoryTree();

        return view($this->_config['view'], compact('sellers', 'categories', 'sellerCategories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->input();

        $sellerCategories = $this->sellerCategoryRepository->find($id);

        if (! isset($data['categories'])) {
            $data['categories'][0] = null;
        }

        $sellerCategories->update($data);

        session()->flash('success', __('marketplace::app.admin.sellers.category.update-success'));

        return redirect()->route('admin.marketplace.seller.category.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        $product = $this->sellerCategoryRepository->findOrFail($id);

        try {
            $this->sellerCategoryRepository->delete($id);

            session()->flash('success', trans('admin::app.response.delete-success', ['name' => 'Seller Category']));

            return response()->json(['message' => true], 200);
        } catch (Exception $e) {
            report($e);

            session()->flash('error', trans('admin::app.response.delete-failed', ['name' => 'Seller Category']));
        }

        return response()->json(['message' => false], 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function massDestroy()
    {
        $data = request()->all();

        $indexs = explode(",", $data['indexes']);

        foreach ($indexs as $id) {

            $sellerCategories = $this->sellerCategoryRepository->find($id);

            $sellerCategories->delete();
        }

        session()->flash('success', __('marketplace::app.admin.sellers.category.delete-success'));

        return redirect()->route('admin.marketplace.seller.category.index');
    }
}
