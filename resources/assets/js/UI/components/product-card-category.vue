<template>
    <div class="product-item">
        <div class="product-item__top">
            <div class="product-item__favorite">
                <a class="active"></a>
            </div>
            <div class="product-item__share">
                <!--yandex share-->
            </div>
            <div class="product-item__image">
                <img
                    loading="lazy"
                    :alt="product.name"
                    :src="product.image || product.product_image"
                    :data-src="product.image || product.product_image"
                    class="card-product__picture-img"
                    :onerror="`this.src='${this.$root.baseUrl}/vendor/webkul/ui/assets/images/product/large-product-placeholder.png'`"/>
            </div>
            <div class="product-item__discount" v-if="product.new">
                {{ product.new }}
            </div>
            <div class="product-item__video-btn"></div>
        </div>
        <div class="product-item__middle">
            <div class="product-item__prices">
                <div class="product-item__price">8 999 <span>₽</span></div>
                <div class="product-item__old-price">15 601 <span>₽</span></div>
            </div>
            <div class="product-item__rates" v-if="product.totalReviews && product.totalReviews > 0">
                <star-ratings :ratings="product.avgRating"></star-ratings>
                <div class="product-item__rate-value">{{product.totalReviews}}</div>
            </div>
        </div>
        <div class="product-item__media">
            <div class="product-item__preview"><img src="/img/demo/image_big.png" alt=""></div>
            <div class="product-item__effects"><span><img src="/img/demo/effect.png" alt=""></span><span><img
                src="/img/demo/effect.png" alt=""></span><span><img src="/img/demo/effect.png" alt=""></span><span><img
                src="/img/demo/effect.png" alt=""></span><span><img src="/img/demo/effect.png" alt=""></span><span><img
                src="/img/demo/effect.png" alt=""></span>
            </div>
        </div>
        <div class="product-item__features">
            <dl>
                <dt>420</dt>
                <dd>зарядов</dd>
            </dl>
            <dl>
                <dt>113₽</dt>
                <dd>1 залп</dd>
            </dl>
            <dl>
                <dt>8</dt>
                <dd>эффектов</dd>
            </dl>
            <dl>
                <dt>1</dt>
                <dd>дюйм</dd>
            </dl>
            <dl>
                <dt>1:30</dt>
                <dd>время</dd>
            </dl>
        </div>
        <div class="product-item__supbottom">
            <div class="product-item__name">{{ product.name }}</div>
            <div class="product-item__delivery">31 декабря</div>
            <!--.product-item__delivery.product-item__delivery_special завтра-->
        </div>
        <div class="product-item__bottom">
            <div class="product-item__buy"><a class="product-item__cart" href="#"></a>
                <div class="product-item__added"><a class="product-item__cart-more" href="#">+1</a><a
                    class="product-item__cart-link" href="/cart.html">в корзину</a></div>
            </div>
            <!-- Сокращается точками при длинном названии-->
            <div class="product-item__seller">Феерия и очень длинное название</div>
        </div>
        <div class="product-item__video-wrapper">
            <div class="product-item__video">
                <video src="https://data.feeriya.ru/video/43822.mp4" mute="" autoplay=""></video>
                <div class="product-item__video-text">скорость x2</div>
            </div>
        </div>
        <a class="product-item__link" :href="`${baseUrl}/${product.slug}`"></a>
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
