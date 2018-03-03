<template>

    <div>

        <list-filter
            :filterKeys="['message', 'class', 'file']">
        </list-filter>

        <div class="row">
            <div class="col-md-12">
                <paginate v-show="pageCount > 1"
                          :pageCount="pageCount"
                          :clickHandler="setCurrentPage"
                          :prevText="'<<'"
                          :nextText="'>>'"
                          :initialPage="currentPage - 1"
                          :forcePage="currentPage - 1"
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
                               :catchable=catchable>
                    </list-item>
                </div>
            </div>
            <div class="col-md-12">
                <paginate v-show="pageCount > 1"
                          :pageCount="pageCount"
                          :clickHandler="setCurrentPage"
                          :prevText="'<<'"
                          :nextText="'>>'"
                          :initialPage="currentPage - 1"
                          :forcePage="currentPage - 1"
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
                requested: false
            };
        },
        computed: mapGetters({
            total: 'total',
            pageCount: 'pageCount',
            currentPage: 'currentPage',
            catchables: 'getAll',
            filter: 'getFilter',
            loading: 'getFetchAllInProgress'
        }),
        components: {
            ListItem,
            Paginate,
            ListFilter
        },
        mounted() {
            this.$store.dispatch('loadCatchables').then((_) => {
                this.requested = true;
            }).catch((error) => {
                this.requested = true;
                this.$notify({
                    group: 'main',
                    classes: 'vue-notification error',
                    title: 'Error',
                    text: error
                });
            });
        },
        methods: {
            setCurrentPage(page) {
                this.$store.dispatch('loadCatchables', {
                    page: page
                }).then((_) => {
                    //
                }).catch((error) => {
                    this.$notify({
                        group: 'main',
                        classes: 'vue-notification error',
                        title: 'Error',
                        text: error
                    });
                })
            },
            onFilterChange(key, value) {
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