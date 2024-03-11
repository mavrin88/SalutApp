<?php

namespace Webkul\MarketplaceWarehouse\Helpers;

use Illuminate\Support\Str;
use Webkul\Attribute\Repositories\AttributeOptionRepository;
class ProductView
{
    /**
     * Returns the visible custom attributes
     *
     * @param  \Webkul\Product\Contracts\Product  $product
     * @return void|array
     */
    public function getAttributes($product) {
        $data = [];

        $attributes = $product->attribute_family->custom_attributes()->where('attributes.is_visible_on_front', 1)->get();

        $attributeOptionRepository = app(AttributeOptionRepository::class);

        foreach ($attributes as $attribute) {
            $value = $product->{$attribute->code};
            $imgUrl = '';
            if ($attribute->type == 'boolean') {
                $value = $value ? 'Yes' : 'No';
            } elseif($value) {
                if ($attribute->type == 'select') {
                    $attributeOption = $attributeOptionRepository->find($value);

                    if ($attributeOption) {
                        $value = $attributeOption->label ?? null;

                    if (str($attributeOption->swatch_value)->isNotEmpty()) {
                        $imgUrl = $attributeOption->swatch_value_url;
                    }
                        if (! $value) {
                            continue;
                        }
                    }
                } elseif (
                    $attribute->type == 'multiselect'
                    || $attribute->type == 'checkbox'
                ) {
                    $labels = [];

                    $attributeOptions = $attributeOptionRepository->findWhereIn('id', explode(",", $value));

                    foreach ($attributeOptions as $attributeOption) {
                        if ($label = $attributeOption->label) {
                            $labels[] = $label;
                        }
                    }

                    $value = implode(", ", $labels);
                }
            }

            $data[$attribute->code] = [
                'id'         => $attribute->id,
                'code'       => $attribute->code,
                'label'      => $attribute->name,
                'value'      => $value,
                'admin_name' => $attribute->admin_name,
                'type'       => $attribute->type
            ];
            if (\str($imgUrl)->isNotEmpty()) {
                $data[$attribute->code]['img']= $imgUrl;
            }
        }

        return $data;
    }

}
