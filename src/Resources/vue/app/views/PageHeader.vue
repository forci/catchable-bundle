<template>

    <nav class="navbar navbar-toggleable-md navbar-light bg-faded">
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="javascript:navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <router-link :to="{ name: 'list' }" class="navbar-brand">Catchable</router-link>

        <ul class="breadcrumb">
            <slot name="breadcrumbs"></slot>
        </ul>

        <ul class="nav navbar-nav ml-md-auto">
            <li>
                <div class="input-group input-group-sm" style="padding-right: 10px;">
                    <input type="text" class="form-control"
                           placeholder="search"
                           :value="filter.message"
                           @input="onFilterMessageChange($event.target.value)"/>
                    <span class="input-group-btn">
                        <button class="btn btn-secondary" type="button" @click="onFilterMessageChange(null)">
                            <i class="fas fa-times"></i>
                        </button>
                    </span>
                </div>
            </li>
            <li>
                <a href="javascript:" class="btn btn-danger btn-sm" @click="deleteAll()">
                    Delete All
                </a>
            </li>
        </ul>
    </nav>

</template>

<script>

    import {mapGetters} from 'vuex'

    export default {
        computed: mapGetters({
            filter: 'getFilter'
        }),
        methods: {
            onFilterMessageChange(value) {
                if (this.filter.message === value) {
                    return; // do not reload
                }

                if (this.$route.name !== 'list') {
                    this.$router.push('/list');
                }

                this.$store.dispatch('updateFilter', {
                    key: 'message',
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
            ),
            deleteAll() {
                let res = confirm('This action will delete all records!');
                if (res) {
                    this.$store.dispatch('deleteAll').then((_) => {
                        this.$router.push('/list');
                    }).catch(error => {
                        this.$notify({
                            group: 'main',
                            classes: 'vue-notification error',
                            title: 'Error',
                            text: error
                        });
                    });
                }
            }
        }
    }

</script>