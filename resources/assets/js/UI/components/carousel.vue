<template>
    <carousel
        :rtl="localeDirection == 'rtl'"
        :dir="localeDirection"
        :id="id"
        :navigationEnabled="true"
        navigationPrevLabel="<span class='carousel-banners__arrow carousel-banners__arrow--prev'></span>"
        navigationNextLabel="<span class='carousel-banners__arrow carousel-banners__arrow--next'></span>"
        :paginationEnabled="true"
        :perPage="parseInt(slidesPerPage)"
        :loop="loop == 'true' ? true : false"
        :autoplay="autoplay == 'true' ? true : false"
        :autoplayTimeout="timeout ? parseInt(timeout) : 2000"
        :autoplayDirection="'forward'"
        :class="[
            localeDirection,
            (navigationEnabled == 'hide') ? 'navigation-hide' : '',
            (paginationEnabled == 'hide') ? 'pagination-hide' : '',
            addClass
        ]">

        <slot
            v-for="index in parseInt(slidesCount)"
            :name="`slide-${parseInt(index) - 1}`">
        </slot>
    </carousel>
</template>

<script type="text/javascript">
    export default {
        props: [
            'id',
            'loop',
            'timeout',
            'autoplay',
            'addClass',
            'direction',
            'slidesCount',
            'slidesPerPage',
            'localeDirection',
            'navigationEnabled',
            'paginationEnabled',
        ],

        data: function () {
            return {}
        },

        methods: {
            slideClicked: function () {
                debugger
            }
        }
    }
</script>
