<template>
    <div class="container">
        <shimmer-component v-if="isLoading"></shimmer-component>

        <template v-else-if="productCollections.length > 0">
            <h2 class="carousel-product-cards__title title-2">
                {{ isCategory ? categoryDetails.name : productTitle }}
            </h2>

            <div class="carousel-product-cards__list" :class="localeDirection">

                <carousel-component
                    :slides-per-page="slidesPerPage"
                    pagination-enabled="hide"
                    navigation-enabled="hide"
                    :id="isCategory ? `${categoryDetails.name}-carousel` : productId"
                    :locale-direction="localeDirection"
                    :slides-count="productCollections.length"
                    v-if="count != 0">

                    <slide
                        :key="index"
                        :slot="`slide-${index}`"
                        v-for="(product, index) in productCollections">
                        <product-card
                            :list="list"
                            :product="product">
                        </product-card>
                    </slide>
                </carousel-component>
            </div>
        </template>
    </div>
</template>

<script>
export default {
    props: {
        count: {
            type: String,
            default: '10'
        },
        productId: {
            type: String,
            default: ''
        },
        productTitle: String,
        productRoute: String,
        localeDirection: String,
        showRecentlyViewed: {
            type: String,
            default: 'false'
        },
        recentlyViewedTitle: String,
        noDataText: String,
    },

    data: function () {
        return {
            list: false,
            isLoading: true,
            isCategory: false,
            productCollections: [],
            slidesPerPage: 6,
            windowWidth: window.innerWidth,
        }
    },

    mounted: function () {
        this.$nextTick(() => {
            window.addEventListener('resize', this.onResize);
        });

        this.getProducts();
        this.setWindowWidth();
        this.setSlidesPerPage(this.windowWidth);
    },

    watch: {
        /* checking the window width */
        windowWidth(newWidth, oldWidth) {
            this.setSlidesPerPage(newWidth);
        }
    },

    methods: {
        /* fetch product collections */
        getProducts: function () {
            this.$http.get(this.productRoute)
                .then(response => {
                    let count = this.count;

                    if (response.data.status && count != 0) {
                        if (response.data.categoryProducts !== undefined) {
                            this.isCategory = true;
                            this.categoryDetails = response.data.categoryDetails;
                            this.productCollections = response.data.categoryProducts;
                        } else {
                            this.productCollections = response.data.products;
                        }
                    } else {
                        this.productCollections = 0;
                    }

                    this.isLoading = false;
                })
                .catch(error => {
                    this.isLoading = false;
                    console.log(this.__('error.something_went_wrong'));
                })
        },

        /* waiting for element */
        waitForElement: function (selector, callback) {
            if (jQuery(selector).length) {
                callback();
            } else {
                setTimeout(() => {
                    this.waitForElement(selector, callback);
                }, 100);
            }
        },

        /* setting window width */
        setWindowWidth: function () {
            let windowClass = this.getWindowClass();

            this.waitForElement(windowClass, () => {
                this.windowWidth = $(windowClass).width();
            });
        },

        /* get window class */
        getWindowClass: function () {
            return this.showRecentlyViewed === 'true'
                ? '.with-recent-viewed'
                : '.without-recent-viewed';
        },

        /* on resize set window width */
        onResize: function () {
            this.windowWidth = $(this.getWindowClass()).width();
        },

        /* setting slides on the basis of window width */
        setSlidesPerPage: function (width) {
            if (width >= 1200) {
                this.slidesPerPage = 6;
            } else if (width < 1200 && width >= 992) {
                this.slidesPerPage = 5;
            } else if (width < 992 && width >= 822) {
                this.slidesPerPage = 4;
            } else if (width < 822 && width >= 626) {
                this.slidesPerPage = 3;
            } else {
                this.slidesPerPage = 2;
            }
        }
    },

    /* removing event */
    beforeDestroy: function () {
        window.removeEventListener('resize', this.onResize);
    },
}
</script>
