<seller-query-form> </seller-query-form>

@push('scripts')
<script type="text/x-template" id="query-form-template">

    <form action="" class="account-table-content" id="query-form" method="POST" data-vv-scope="query-form" @submit.prevent="sellerQuery('query-form')">

        @csrf

        <div class="form-container">

            <div class="form-group" :class="[errors.has('contact-form.name') ? 'has-error' : '']">
                <label for="name" class="required mandatory">{{ __('marketplace::app.shop.sellers.profile.name') }}</label>
                <input type="text" v-model="contact.name" class="form-style control" name="name" v-validate="'required'" data-vv-as="&quot;{{ __('marketplace::app.shop.sellers.profile.name') }}&quot;">
                <span class="control-error" v-if="errors.has('contact-form.name')">@{{ errors.first('contact-form.name') }}</span>
            </div>

            <div class="form-group" :class="[errors.has('contact-form.email') ? 'has-error' : '']">
                <label for="email" class="required mandatory">{{ __('marketplace::app.shop.sellers.profile.email') }}</label>
                <input type="text" v-model="contact.email" class="form-style control" name="email" v-validate="'required|email'" data-vv-as="&quot;{{ __('marketplace::app.shop.sellers.profile.email') }}&quot;">
                <span class="control-error" v-if="errors.has('contact-form.email')">@{{ errors.first('contact-form.email') }}</span>
            </div>

            <div class="form-group" :class="[errors.has('contact-form.subject') ? 'has-error' : '']">
                <label for="subject" class="required mandatory">{{ __('marketplace::app.shop.sellers.profile.subject') }}</label>
                <input type="text" v-model="contact.subject" class="control form-style" name="subject" v-validate="'required'" data-vv-as="&quot;{{ __('marketplace::app.shop.sellers.profile.subject') }}&quot;">
                <span class="control-error" v-if="errors.has('contact-form.subject')">@{{ errors.first('contact-form.subject') }}</span>
            </div>

            <div class="form-group" :class="[errors.has('contact-form.query') ? 'has-error' : '']">
                <label for="query" class="required mandatory">{{ __('marketplace::app.shop.sellers.profile.query') }}</label>
                <textarea class="control form-style" v-model="contact.query" name="query" v-validate="'required'"  data-vv-as="&quot;{{ __('marketplace::app.shop.sellers.profile.query') }}&quot;">
                </textarea>
                <span class="control-error" v-if="errors.has('contact-form.query')">@{{ errors.first('contact-form.query') }}</span>
            </div>

                {!! Captcha::render() !!}

            <button type="submit" class="btn btn-lg btn-primary theme-btn" :disabled="disable_button"

            >
                {{ __('marketplace::app.shop.sellers.profile.submit') }}
            </button>

        </div>

    </form>

</script>

<script>
    Vue.component('seller-query-form', {

        data: () => ({
            contact: {
                'name': '',
                'email': '',
                'subject': '',
                'query': ''
            },

            disable_button: false,
        }),

        template: '#query-form-template',

        created () {

            @auth('customer')
                @if(auth('customer')->user())
                    this.contact.email = "{{ auth('customer')->user()->email }}";
                    this.contact.name = "{{ auth('customer')->user()->first_name }} {{ auth('customer')->user()->last_name }}";
                @endif
            @endauth

        },

        methods: {
            sellerQuery (formScope) {
                var this_this = this;

                this_this.disable_button = true;

                this.$validator.validateAll(formScope).then((result) => {
                    if (result) {

                        this.$http.post("{{ route('marketplace.seller.query') }}", this.contact)
                            .then (function(response) {
                                this_this.disable_button = false;

                                this_this.$parent.closeModal();

                                window.showAlert(
                                        `alert-success`,
                                        'Success',
                                        response.data.message
                                );

                            })

                            .catch (function (error) {
                                this_this.disable_button = false;

                                this_this.handleErrorResponse(error.response, 'query-form')
                            })
                    } else {
                        this_this.disable_button = false;
                    }
                });
            },

            handleErrorResponse (response, scope) {
                if (response.status == 422) {
                    serverErrors = response.data.errors;
                    this.$root.addServerErrors(scope)
                }
            }
        }
    });

</script>
@endpush