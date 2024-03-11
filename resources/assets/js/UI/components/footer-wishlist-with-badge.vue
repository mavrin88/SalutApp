<template>
    <div class="footer__bar-item"><a class="footer__bar-item-link" :href="src"></a>
        <div class="footer__bar-item-main">
            <div class="footer__bar-item-icon">
                <svg class="footer__bar-item-icon-svg">
                    <use class="footer__bar-item-icon-use" href="/themes/salutarium/assets/images/sprite.svg#i-bar-3"></use>
                </svg>
            </div>
            <div class="footer__bar-item-counter">{{ wishlistCount }}</div>
        </div>
        <div class="footer__bar-item-text">Избранное</div>
    </div>
<!--        <div class="header__bar-list-item-text" v-text="__('header.wishlist')" v-if="isText == 'true'"></div>-->
</template>

<script type="text/javascript">
export default {
    props: ['isCustomer', 'isText', 'src','wishlistItemCount'],

    data: function() {
        return {
            wishlistCount: 0
        };
    },

    created: function() {
        setTimeout(function() {
            this.wishlistCount = this.wishlistItemCount;
        }.bind(this), 200);
    },
};
</script>
