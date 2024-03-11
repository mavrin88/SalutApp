<template>
    <li class="header__bar-list-item"><a class="header__bar-list-item-link" :href="viewCartRoute"></a>
        <div class="header__bar-list-item-main">
            <div class="header__bar-list-item-icon">
                <svg class="header__bar-list-item-icon-svg">
                    <use class="header__bar-list-item-icon-use" href="/themes/salutarium/assets/img/sprite.svg#i-bar-4"></use>
                </svg>
            </div>
            <div class="header__bar-list-item-counter">{{ cartItems.length }}</div>
        </div>
        <div class="header__bar-list-item-text">{{ cartText }}</div>
    </li>
</template>

<script>
export default {
    props: ['viewCartRoute', 'cartText'],

    data: function () {
        return {
            cartItems: [],
        }
    },
    mounted: function () {
        this.getMiniCartDetails();
    },

    watch: {
        '$root.miniCartKey': function () {
            this.getMiniCartDetails();
        }
    },
    methods: {
        getMiniCartDetails: function () {
            this.$http.get(`${this.$root.baseUrl}/mini-cart`)
                .then(response => {
                    if (response.data.status) {
                        this.cartItems = response.data.mini_cart.cart_items;
                    }
                })
                .catch(exception => {
                    console.log(this.__('error.something_went_wrong'));
                    console.log(`${this.$root.baseUrl}/mini-cart`);
                });
        }
    }
};
</script>
