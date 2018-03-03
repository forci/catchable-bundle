<template>

    <div class="row">
        <div class="col-md-12" style="display:flex;">

            <div class="input-group" style="width:25%; margin:5px;"
                 v-for="filterKey in filterKeys">
                <input type="text" class="form-control input-sm"
                       :placeholder="filterKey"
                       :value="filter[filterKey]"
                       @input="onFilterChange(filterKey, $event.target.value)"/>
                <span class="input-group-btn">
                    <button class="btn btn-secondary" type="button" @click="onFilterChange(filterKey, null)">
                        <i class="fas fa-times"></i>
                    </button>
                </span>
            </div>
        </div>
    </div>

</template>

<script>

    import {mapGetters} from 'vuex'

    export default {
        props: ['filterKeys'],
        computed: mapGetters({
            filter: 'getFilter'
        }),
        methods: {
            onFilterChange(key, value) {
                if (this.filter[key] === value) {
                    return; // do not reload
                }

                this.$store.dispatch('updateFilter', {
                    key: key,
                    value: value
                });

                this.search(this);
            },
            search: debounce(
                (that) => {
                    that.$store.dispatch('loadCatchables', {
                        page: 1
                    });
                },
                500
            )
        }
    }

</script>