<script>
export default {
    name: 'mp-tree-view',

    inheritAttrs: false,

    props: {
        inputType: {
            type: String,
            required: false,
            default: 'checkbox'
        },

        nameField: {
            type: String,
            required: false,
            default: 'permissions'
        },

        idField: {
            type: String,
            required: false,
            default: 'id'
        },

        valueField: {
            type: String,
            required: false,
            default: 'value'
        },

        captionField: {
            type: String,
            required: false,
            default: 'name'
        },

        childrenField: {
            type: String,
            required: false,
            default: 'children'
        },

        items: {
            type: [Array, String, Object],
            required: false,
            default: () => ([])
        },

        behavior: {
            type: String,
            required: false,
            default: 'reactive'
        },

        value: {
            type: [Array, String, Object],
            required: false,
            default: () => ([])
        },

        fallbackLocale: {
            type: String,
            required: false
        },
    },

    data() {
        return {
            finalValues: []
        }
    },

    computed: {
        savedValues () {
            if(! this.value)
                return [];

            if(this.inputType == 'radio')
                return [this.value];

            return (typeof this.value == 'string') ? JSON.parse(this.value) : this.value;
        }
    },


    methods: {
        generateChildren () {
            let childElements = [];

            let items = (typeof this.items == 'string') ? JSON.parse(this.items) : this.items;

            for(let key in items) {
                childElements.push(this.generateTreeItem(items[key]));
            }

            return childElements;
        },

        generateTreeItem(item) {
            return this.$createElement('mp-tree-item', {
                props: {
                    items: item,
                    value: this.finalValues,
                    savedValues: this.savedValues,
                    nameField: this.nameField,
                    inputType: this.inputType,
                    captionField: this.captionField,
                    childrenField: this.childrenField,
                    valueField: this.valueField,
                    idField: this.idField,
                    behavior: this.behavior,
                    fallbackLocale: this.fallbackLocale
                },
                on: {
                    input: selection => {
                        this.finalValues = selection;
                    }
                },
            })
        }
    },

    render (createElement) {
        return createElement('div', {
                class: [
                    'tree-container',
                ]
            }, [this.generateChildren()]
        )
    }
}
</script>
