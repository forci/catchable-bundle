<template>

    <div :id="'catchable_' + catchable.id" class="card">
        <div class="card-block">
            <h4 class="card-title mb-3">
                <router-link :to="{ name: 'view', params: { id: catchable.id} }">{{ catchable.class }}</router-link>
                <router-link class="btn btn-primary btn-sm float-right" :to="{ name: 'view', params: { id: catchable.id} }">View</router-link>
            </h4>

            <h5 class="card-subtitle mb-3 text-muted">
                {{ catchable.message }}
            </h5>

            <h6 class="card-subtitle mb-2 text-muted">{{ catchable.createdAt | formatDate }}</h6>
            <!--<p class="card-text new-line">{{ catchable.message }}</p>-->
            <p class="card-text new-line">In {{ catchable.file }} (line {{ catchable.line }})</p>
            <a href="javascript:" class="btn btn-danger btn-sm float-right" @click="deleteRecord()">
                Delete
            </a>
        </div>
    </div>

</template>

<script>

    export default {
        props: ['catchable'],
        methods: {
            deleteRecord() {
                this.$store.dispatch('deleteCatchable', {
                    id: this.catchable.id
                }).then((_) => {
                    //
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

</script>