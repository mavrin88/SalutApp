@if ($categories->count())
<accordian :title="'{{ __('admin::app.catalog.products.categories') }}'" :active="false">
    <div slot="header">
        {{ __('admin::app.catalog.products.categories') }}
        <i class="icon expand-icon right"></i>
    </div>
    <div slot="body">
        
        {!! view_render_event('marketplace.sellers.account.catalog.product.edit_form_accordian.categories.controls.before', ['product' => $product]) !!}
{{--
        @foreach ($categories as $category)
            @if (is_null($category->ancestors))
                <li class="root">{{$category->name}}</li>
            @else
                 @foreach ($category->ancestors as $ancestor)
                    <li class="ancestor">{{$ancestor->name}}</li>
                 @endforeach
                 <li class="child">{{$category->name}}</li>
            @endif
        @endforeach --}}

        <!-- <div class="tree-container">
            <mp-tree-view  :store-categories ="storeCategories"  ></mp-tree-view>
        </div> -->

        <tree-view behavior="normal" value-field="id" name-field="categories" input-type="checkbox" items='@json($categories)' value='@json($product->categories->pluck("id"))'></tree-view>

        {!! view_render_event('marketplace.sellers.account.catalog.product.edit_form_accordian.categories.controls.after', ['product' => $product]) !!}

    </div>
</accordian>
@endif

@push('scripts')

<script type="text/x-template" id="mp-tree-view">
    <div>
        <div class="tree-item active">
            <span class="checkbox">
                <input type="checkbox" id="{{$rootCategory->id}}" name="categories[]" :value="{{$rootCategory->id}}">
                <label for="{{$rootCategory->id}}" class="checkbox-view"></label>
                <span for="{{$rootCategory->id}}"> {{$rootCategory->name}}</span>
            </span>

        <div class="tree-item active" v-for="category in storeCategories" v-if="category.id != 1">

            <div class="tree-item active" v-for="ancestorcategory in category.ancestors" v-if="ancestorcategory.id != 1">
                <span class="checkbox" >
                    <input type="checkbox" :id="ancestorcategory.id" name="categories[]" :value="ancestorcategory.id">
                    <label :for="ancestorcategory.id" class="checkbox-view"></label>
                    <span :for="ancestorcategory.id">@{{ ancestorcategory.name }}</span>
                </span>
            </div>
            
            <span class="checkbox">
                <input type="checkbox" :id="category.id" name="categories[]" :value="category.id">
                <label :for="category.id" class="checkbox-view"></label>
                <span :for="category.id">@{{ category.name }}</span>
            </span>
        </div>
        </div>
    </div>
</script>

<script>
    let storeCategories =  @json($categories)

    Vue.component('mp-tree-view', {
        template: '#mp-tree-view',
        props: ['storeCategories'],
        data() {
            return {

            }
        },
        
        methods: {
            getAncestors: function () {

                let ancestors = [];

                storeCategories.forEach(category => {
                    category.ancestors.forEach(ancestor=> {

                        let nodeNotExists = ancestors.filter(anctr =>
                             anctr.id != ancestor.id
                         )

                        if (! ancestors.length) {
                            ancestors.push( ancestor)
                        } else if ( nodeNotExists.length) {
                            ancestors.push( ancestor)
                        }
                    })
                    // ancestors.push(category)
                });

                return ancestors;
            },
        }
    });
</script>

@endpush