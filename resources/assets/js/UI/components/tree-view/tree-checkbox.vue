<template>
    <span class="checkbox">
        <input type="checkbox" :id="'image' + id" :name="[nameField + '[]']" :value="modelValue" :checked="isActive">
        <label class="checkbox-view" :for="'image' + id"></label>
        <span class="" :for="'image' + id">{{ label }}</span>
    </span>
</template>

<script>
export default {
    name: 'mp-tree-checkbox',

    props: ['id', 'label', 'nameField', 'modelValue', 'inputValue', 'value'],

    computed: {
        isMultiple () {
            return Array.isArray(this.internalValue)
        },

        isActive () {
            const value = this.value
            const input = this.internalValue

            if (this.isMultiple) {
                return input.some(item => this.valueComparator(item, value))
            }

            return value ? this.valueComparator(value, input) : Boolean(input)
        },

        internalValue: {
            get () {
                return this.lazyValue
            },

            set (val) {
                this.lazyValue = val
                this.$emit('input', val)
            }
        }
    },

    data: vm => ({
        lazyValue: vm.inputValue
    }),

    watch: {
        inputValue (val) {
            this.internalValue = val
        }
    },

    methods: {
        inputChanged () {
            const value = this.value
            let input = this.internalValue

            if (this.isMultiple) {
                const length = input.length

                input = input.filter(item => !this.valueComparator(item, value))

                if (input.length === length) {
                    input.push(value)
                }
            } else {
                input = !input
            }

            this.$emit('change', input)
        },

        valueComparator (a, b) {
            if (a === b)
                return true

            if (a !== Object(a) || b !== Object(b)) {
                return false
            }

            const props = Object.keys(a)

            if (props.length !== Object.keys(b).length) {
                return false
            }

            return props.every(p => this.valueComparator(a[p], b[p]))
        }
    }
}
</script>
