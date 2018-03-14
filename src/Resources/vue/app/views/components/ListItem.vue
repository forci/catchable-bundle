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
            <div class="btn-group float-right">
                <a href="javascript:" ref="deleteExceptionButton" class="btn btn-danger btn-sm" @click="deleteRecord()">
                    <i class="fa fa-trash-alt"></i>
                    Exception
                </a>
                <a href="javascript:" ref="deleteClassButton" class="btn btn-danger btn-sm" @click="deleteClass()">
                    <i class="fa fa-trash-alt"></i>
                    Class
                </a>
            </div>
            <bootstrap-tooltip :target="() => $refs.deleteExceptionButton">
                Delete this Exception
            </bootstrap-tooltip>
            <bootstrap-tooltip :target="() => $refs.deleteClassButton">
                Delete all exceptions of this class
            </bootstrap-tooltip>
        </div>
    </div>

</template>

<script>

    import BootstrapTooltip from 'bootstrap-vue/es/components/tooltip/tooltip'

    export default {
        props: ['catchable'],
        components: {
            BootstrapTooltip
        },
        methods: {
            deleteRecord() {
                this.$store.dispatch('deleteCatchable', {
                    id: this.catchable.id
                }).then((_) => {
                    this.$emit('catchableRemovedById', {id: this.catchable.id});
                }).catch(error => {
                    this.$notify({
                        group: 'main',
                        classes: 'vue-notification error',
                        title: 'Error',
                        text: error
                    });
                });
            },
            deleteClass() {
                this.$store.dispatch('deleteCatchable', {
                    class: this.catchable.class
                }).then((_) => {
                    this.$emit('catchableRemovedByClass', {class: this.catchable.class});
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