<template>
    <form method="POST" @submit.prevent="addToCart">
        <div class="product-item__added" v-if="this.$root.miniCartKey > 0">
            <button class="product-item__cart-more" type="submit">+1</button>
            <a class="product-item__cart-link" :href="checkoutRoute">{{btnText}}</a>
        </div>
        <button v-else
            type="submit"
            :disabled="isButtonEnable == 'false' || isButtonEnable == false"
            :class=" !!buttonWithText ? 'form-submit btn btn_dark btn_cart' : 'product-item__cart'"
        >
           {{ !!buttonWithText ? btnText : ''}}
        </button>
    </form>
</template>

<script>
export default {
    props: [
        'form',
        'btnText',
        'isEnable',
        'csrfToken',
        'productId',
        'reloadPage',
        'moveToCart',
        'checkoutRoute',
        'buttonWithText'
    ],

    data: function () {
        return {
            'isButtonEnable': this.isEnable,
            'qtyText': this.__('checkout.qty'),
        }
    },

    methods: {
        'addToCart': function () {
            this.isButtonEnable = false;
            let url = `${this.$root.baseUrl}/cart/add`;
            let quantity = parseInt(document.querySelector('.qty__input')?.value)
            this.$http.post(url, {
                'quantity': !isNaN(quantity) ? quantity : 1,
                'product_id': this.productId,
                '_token': this.csrfToken.split("&#039;").join(""),
            })
                .then(response => {
                     this.isButtonEnable = true;

                    if (response.data.status == 'success') {
                        this.$root.miniCartKey++;

                        window.showAlert(`alert-success`, this.__('shop.general.alert.success'), response.data.message);

                        if (this.reloadPage == "1") {
                            window.location.reload();
                        }
                    } else {
                        if (response.data.redirectionRoute) {
                            window.location.href = response.data.redirectionRoute;
                        }
                    }
                })
                .catch(error => {
                    console.log(this.__('error.something_went_wrong'));
                })
        },
    }
}
</script>
