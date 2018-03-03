<template>

    <div class="container">
        <page-header>
            <template slot="breadcrumbs">

                <!--<template v-if="currentRoute === 'list'">-->
                    <!--<li class="breadcrumb-item">List</li>-->
                <!--</template>-->

                <li class="breadcrumb-item">
                    <router-link :to="{ name: 'list' }">Errors</router-link>
                </li>

                <template v-if="currentRoute === 'view' && record">
                    <li class="breadcrumb-item">{{ record.id }}</li>
                    <li class="breadcrumb-item">View</li>
                    <li class="breadcrumb-item">{{ record.class }}</li>
                </template>

            </template>
        </page-header>

        <notifications group="main"/>

        <transition name="fade">
            <router-view/>
        </transition>
    </div>

</template>

<script>

    import {mapGetters} from 'vuex'
    import PageHeader from './PageHeader.vue'

    export default {
        components: {
            PageHeader
        },
        computed: {
            ...mapGetters({
                record: 'getCurrentRecord'
            }),
            currentRoute() {
                return this.$route.name;
            }
        }
    }

</script>