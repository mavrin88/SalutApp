<template>
    <div
        :class="`stars fs${size ? size : '16'} ${
            pushClass ? pushClass : ''
        }`"
    >
        <input
            v-if="editable"
            type="number"
            :value="showFilled"
            name="rating"
            class="d-none"
        />

        <i
            :class="`material-icons ${editable ? 'cursor-pointer' : ''}`"
            v-for="(rating, index) in parseInt(
                showFilled != 'undefined' ? showFilled : 3
            )"
            :key="`${index}${Math.random()}`"
            @click="updateRating(index + 1)"
        >
            star
        </i>

        <template v-if="!hideBlank">
            <i
                :class="`material-icons ${editable ? 'cursor-pointer' : ''}`"
                v-for="(blankStar, index) in 5 -
                (showFilled != 'undefined' ? showFilled : 3)"
                :key="`${index}${Math.random()}`"
                @click="updateRating(showFilled + index + 1)"
            >
                star_border
            </i>
        </template>
    </div>
</template>

<style lang="scss">
/**
 * Font size 18px till 420px screen width.
 */
.stars .material-icons {
    font-size: 12px;
    color: #cd8e25;
}

</style>

<script type="text/javascript">
export default {
    props: ['size', 'ratings', 'editable', 'hideBlank', 'pushClass'],

    data: function () {
        return {
            showFilled: this.ratings,
        };
    },

    methods: {
        updateRating: function (index) {
            index = Math.abs(index);
            this.editable ? (this.showFilled = index) : '';
        },
    },
};
</script>
