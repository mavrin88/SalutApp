<template>
    <div class="search-field">
        <button
            class="search-field__button"
            type="button"
            id="header-search-icon"
            aria-label="Search"
            @click="submitForm"
        ></button>
        <input
            autocomplete="off"
            required
            name="term"
            type="search"
            class="search-field__input"
            :placeholder="__('header.search-text')"
            aria-label="Search"
            ref="searchInput"
            v-model:value="inputVal"
            @mouseover="focusInput"
        />
    </div>
</template>

<script type="text/javascript">
export default {
    data: function () {
        return {
            inputVal: '',
            searchedQuery: []
        };
    },

    created: function () {
        let searchedItem = window.location.search.replace('?', '');
        searchedItem = searchedItem.split('&');

        let updatedSearchedCollection = {};

        searchedItem.forEach(item => {
            let splitedItem = item.split('=');
            updatedSearchedCollection[splitedItem[0]] = decodeURI(
                splitedItem[1]
            );
        });

        if (updatedSearchedCollection['image-search'] == 1) {
            updatedSearchedCollection.term = '';
        }

        this.searchedQuery = updatedSearchedCollection;

        if (this.searchedQuery.term) {
            this.inputVal = decodeURIComponent(
                this.searchedQuery.term.split('+').join(' ')
            );
        }
    },
    methods: {
        submitForm: function () {
            if (this.inputVal !== '') {
                $('input[name=term]').val(this.inputVal);
                $('#search-form').submit();
            }
        },
        focusInput: function () {
            this.$refs.searchInput.focus()
        }
    }
};
</script>
