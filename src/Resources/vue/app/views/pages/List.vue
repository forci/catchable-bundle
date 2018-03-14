<template>

    <div>

        <div class="row">
            <div class="col-md-12" style="display:flex;">

                <div class="input-group" style="width:25%; margin:5px;">
                    <select v-model="filter.limit" class="form-control input-sm"
                            @input="updateLimit($event.target.value)">
                        <option v-for="option in limitOptions" :value="option">
                            {{ option }}
                        </option>
                    </select>
                </div>

                <div class="input-group" style="width:25%; margin:5px;"
                     v-for="filterKey in filterFields">
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

        <div class="row">
            <div class="col-md-12">
                <paginate v-show="pageCount > 1"
                          :pageCount="pageCount"
                          :clickHandler="setCurrentPage"
                          :prevText="'<<'"
                          :nextText="'>>'"
                          :initialPage="this.filter.page - 1"
                          :forcePage="this.filter.page - 1"
                          :container-class="'pagination float-right'"
                          :page-class="'page-item'"
                          :page-link-class="'page-link'"
                          :prev-class="'page-item'"
                          :prev-link-class="'page-link'"
                          :next-class="'page-item'"
                          :next-link-class="'page-link'">
                </paginate>
            </div>
            <div class="col-md-12">
                <div class="panel-group" v-loading="loading">
                    <list-item v-for="catchable in catchables"
                               :name="'catchable' + catchable.id"
                               :ref="'catchable-row-' + catchable.id"
                               :key="'row' + catchable.id"
                               :catchable=catchable
                               @catchableRemovedById="onRemoveById"
                               @catchableRemovedByClass="onRemoveByClass"
                    >
                    </list-item>
                </div>
            </div>
            <div class="col-md-12">
                <paginate v-show="pageCount > 1"
                          :pageCount="pageCount"
                          :clickHandler="setCurrentPage"
                          :prevText="'<<'"
                          :nextText="'>>'"
                          :initialPage="this.filter.page - 1"
                          :forcePage="this.filter.page - 1"
                          :container-class="'pagination float-right'"
                          :page-class="'page-item'"
                          :page-link-class="'page-link'"
                          :prev-class="'page-item'"
                          :prev-link-class="'page-link'"
                          :next-class="'page-item'"
                          :next-link-class="'page-link'">
                </paginate>
            </div>
        </div>
    </div>

</template>

<script>

    import {mapGetters} from 'vuex'
    import Paginate from 'vuejs-paginate'
    import ListItem from '../components/ListItem.vue';
    import ListFilter from '../components/ListFilter.vue';

    export default {
        data() {
            return {
                filterFields: ['message', 'class', 'file'],
                limitOptions: [10, 20, 50],
                catchables: [],
                totalCount: 0
            };
        },
        computed: {
            ...mapGetters({
                loading: 'getFetchAllInProgress'
            }),
            pageCount: {
                get() {
                    return !this.totalCount ? 0 : Math.ceil(this.totalCount / this.filter.limit);
                },
                set() {

                }
            },
            filter: {
                get() {
                    return Object.assign({
                        message: null,
                        class: null,
                        file: null,
                        limit: 10,
                        page: 1
                    }, this.$route.query);
                },
                set() {
                }
            }
        },
        components: {
            ListItem,
            Paginate,
            ListFilter
        },
        mounted() {
            this.search();
        },
        watch: {
            'filter': function () {
                this.search();
            }
        },
        methods: {
            updateFilter(values) {
                this.$router.replace({query: Object.assign(this.filter, values)});
            },
            setCurrentPage(page) {
                this.updateFilter({page});
            },
            onFilterChange: debounce(function (key, value) {
                if (this.filter[key] === value) {
                    return; // do not reload
                }

                let values = {
                    page: 1
                };
                values[key] = value;

                this.updateFilter(values);
            }, 500),
            updateLimit(value) {
                if (this.filter.limit === value) {
                    return;
                }

                this.updateFilter({
                    page: 1,
                    limit: value
                });
            },
            search() {
                return this.$store.dispatch('getCatchables', this.filter)
                    .then(response => {
                        this.catchables = response.entities;
                        this.totalCount = response.total;
                        this.filter.page = response.page;

                        return response;
                    });
            },
            onRemoveById() {
                this.search()
                    .then(response => {
                        if (!response.entities.length && response.page > 1) {
                            this.updateFilter({
                                page: response.page - 1
                            })
                        }
                    });
            },
            onRemoveByClass() {
                this.$router.replace({
                    name: 'list',
                    query: Object.assign(this.filter, {
                        class: null,
                        page: 1
                    })
                });
            }
        }
    }

</script>