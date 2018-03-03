<template>

    <div v-if="record">
        <div class="card">
            <div class="card-block">
                <catchable
                    v-loading="loading"
                    :catchable=record>
                </catchable>
            </div>
        </div>
    </div>

</template>

<script>

    import {mapGetters} from 'vuex'
    import Catchable from '../components/Catchable.vue';

    export default {
        mounted() {
            this.$store.dispatch('getCatchable', {
                id: parseInt(this.$route.params.id)
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
        components: {
            Catchable
        },
        computed: mapGetters({
            record: 'getCurrentRecord',
            loading: 'getFetchInProgress'
        })
    }

</script>