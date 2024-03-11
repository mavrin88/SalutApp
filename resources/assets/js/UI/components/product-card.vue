<template>
        <div class="card-product">
        <a :href="`${baseUrl}/${product.slug}`" :title="product.name" class="card-product__picture">
            <img
                loading="lazy"
                :alt="product.name"
                :src="product.image || product.product_image"
                :data-src="product.image || product.product_image"
                class="card-product__picture-img"
                :onerror="`this.src='${this.$root.baseUrl}/vendor/webkul/ui/assets/images/product/large-product-placeholder.png'`" />
                <!-- :src="`${$root.baseUrl}/vendor/webkul/ui/assets/images/product/medium-product-placeholder.png`" /> -->
            <div class="card-product__picture-die" v-if="product.new">
                {{ product.new }}
            </div>
            <product-quick-view-btn :quick-view-details="product"></product-quick-view-btn>
        </a>
        <div class="card-product__head">
            <div class="card-product__price" v-html="product.priceHTML" />
            <div class="card-product__rating"></div>
        </div>
        <ul class="card-product__options">
            <li class="card-product__options-item">
                <div class="card-product__options-value">420</div>
                <div class="card-product__options-propertie">зарядов</div>
            </li>
            <li class="card-product__options-item">
                <div class="card-product__options-value">79<span>₽</span></div>
                <div class="card-product__options-propertie">1 залп</div>
            </li>
            <li class="card-product__options-item">
                <div class="card-product__options-value">1,25</div>
                <div class="card-product__options-propertie">дюйма</div>
            </li>
            <li class="card-product__options-item">
                <div class="card-product__options-value">1:30</div>
                <div class="card-product__options-propertie">время</div>
            </li>
        </ul>
        <div class="card-product__name">{{ product.name }}</div>
            <vnode-injector :nodes="getDynamicHTML(product.addToCartHtml)"></vnode-injector>
    </div>
</template>

<script type="text/javascript">
    export default {
        props: [
            'list',
            'product',
        ],

        data: function () {
            return {
                'addToCart': 0,
                'addToCartHtml': '',
            }
        },

        methods: {
            'isMobile': function () {
                if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                    return true;
                } else {
                    return false;
                }
            },
        }
    }
</script>
