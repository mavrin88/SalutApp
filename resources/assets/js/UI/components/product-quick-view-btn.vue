<template>
    <div
        id="quick-view-btn-container"
        class="card-product__picture-play"
        @click="openQuickView({event: $event})">
        <button type="button">
            <img class="card-product__picture-play-img" src="/themes/salutarium/assets/img/play.svg" alt="">
        </button>
    </div>
</template>

<script type="text/javascript">
    export default {
        props: ['quickViewDetails'],

        methods: {
            openQuickView: function ({event}) {
                event.preventDefault();
                event.stopPropagation();

                this.$root.quickView = true;
                this.$root.productDetails = this.quickViewDetails;

                $('body').toggleClass('overflow-hidden');
            },
        }
    }
</script>
