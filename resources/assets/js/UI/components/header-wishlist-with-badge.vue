<template>
    <li class="header__bar-list-item">
        <a class="header__bar-list-item-link" :href="src"></a>
        <div class="header__bar-list-item-main">
            <div class="header__bar-list-item-icon">
                <svg class="header__bar-list-item-icon-svg">
                    <use class="header__bar-list-item-icon-use" href="/themes/salutarium/assets/img/sprite.svg#i-bar-3"></use>
                </svg>
            </div>
            <div class="header__bar-list-item-counter">
                {{ wishlistCount }}
            </div>
        </div>
        <div class="header__bar-list-item-text">
            Избранное
<!--            {{ __('header.wishlist') }}-->
        </div>
    </li>
</template>

<script type="text/javascript">
export default {
    props: ['isCustomer', 'isText', 'src', 'itemsCountSrc'],

    data: function() {
        return {
            wishlistCount: 0
        };
    },
    watch: {
        '$root.headerItemsCount': function () {
            this.updateHeaderItemsCount();
        },
    },

    mounted () {
        this.updateHeaderItemsCount();
    },

    methods: {
        updateHeaderItemsCount: async function () {
            if (this.isCustomer) {
                const response = await fetch(this.itemsCountSrc);
                const data = await response.json();

                this.wishlistCount = data.wishlistedProductsCount;
            }
        },
    },
};
</script>
