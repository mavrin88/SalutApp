<?php

namespace Webkul\Marketplace\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellerForm extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'url'        => ['required', 'unique:marketplace_sellers,url,' . $this->route('id'), new \Webkul\Core\Contracts\Validations\Slug],
            'shop_title' => 'required',
            'phone'      => 'required|numeric',
            'address1'   => 'required',
            'city'       => 'required',
            'postcode'   => 'required',
            'state'      => 'required',
            'country'    => 'required',
            'logo.*'     => 'nullable|mimes:bmp,jpeg,jpg,png,webp|max:2048',
            'banner.*'   => 'nullable|mimes:bmp,jpeg,jpg,png,webp|max:6144',
        ];
    }

    public function attributes()
    {
        return [
            'logo.*'   => trans('marketplace::app.shop.sellers.account.profile.logo'),
            'banner.*' => trans('marketplace::app.shop.sellers.account.profile.banner'),
        ];
    }
}
