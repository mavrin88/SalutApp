<template>
    <div class="modal" v-if="isModalOpen">
        <div class="modal__wrapper">
            <div class="modal__header">
                <slot name="header">
                    Default header
                </slot>
                <div class="modal__close" @click="closeModal"></div>
            </div>

            <div class="modal__content">
                <slot name="body">
                    Default body
                </slot>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['id', 'isOpen'],

        data: function () {
            return {}
        },

        computed: {
            isModalOpen: function () {
                this.addClassToBody();

                return this.isOpen;
            }
        },

        methods: {
            closeModal: function () {
                this.$root.$set(this.$root.modalIds, this.id, false);
            },

            addClassToBody: function () {
                var overlay = document.querySelector(".modal__overlay");

                if(this.isOpen) {
                    overlay.style.display = 'block'
                } else {
                    overlay.style.display = 'none'
                }
            }
        }
    }
</script>
