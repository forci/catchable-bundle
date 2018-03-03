<template>

    <div :id="'catchable_' + catchable.id">

        <h4 class="mb-3">
            <span class="badge badge-info">Message</span>
            {{ catchable.message }}
        </h4>

        <h5 class="card-subtitle mb-2 text-muted">
            {{ catchable.createdAt | formatDate }}
        </h5>

        <h5 class="">
            <span class="badge badge-warning">Class</span>
            <code>{{ catchable.class }}</code>
        </h5>

        <h6 class="">
            <span class="badge badge-warning">File</span>
            <em>
                {{ catchable.file }}:{{ catchable.line }}
            </em>
        </h6>

        <div @click="toggleStringTrace" v-if="!stringTraceVisible">

            <p v-for="(line, key) in catchable.trace"
               style="font-size: 13px; margin: 0 0 5px 0;">
                        <span class="text-secondary">
                            #{{ key|def('') }}
                            {{ line.file|def('') }}
                            (<span class="text-danger">{{ line.line|def('') }}</span>)
                        </span>
                <br/>
                <span style="margin-left: 40px;" class="text-primary">{{ line.class|def('') }}->{{ line.function|def('') }}</span>
            </p>
        </div>

        <transition name="slide-fade">
            <div @click="toggleStringTrace">
                <p class="card-text new-line" v-if="stringTraceVisible">{{ catchable.stackTraceString }}</p>
            </div>
        </transition>

        <div style="margin-left: 60px;" v-if="catchable.previous">
            <catchable :ref="'catchable-row-' + catchable.previous.id"
                       :key="'row' + catchable.previous.id"
                       :catchable=catchable.previous>
            </catchable>
        </div>

        <template v-if="catchable.logs">
            <hr/>
            <h3 style="margin-bottom: 20px;" class="text-info text-center">
                Symfony Logger Logs
            </h3>


            <ul class="list-group list-group-flush">
                <log v-for="(log, index) in catchable.logs"
                     :key="'log_' + catchable.id + '_' + index"
                     :log="log">
                </log>
            </ul>

        </template>
    </div>

</template>

<script>

    import Log from './Log.vue';

    export default {
        props: ['catchable'],
        name: 'catchable',
        data() {
            return {
                previousVisible: true,
                stringTraceVisible: false
            };
        },
        components: {
            Log
        },
        filters: {
            def: function (value, def) {
                if (typeof(value) !== 'undefined' && null !== value) {
                    return value;
                }

                return def;
            }
        },
        methods: {
            togglePrevious() {
                this.$data.previousVisible = !this.previousVisible;
            },
            toggleStringTrace() {
                this.$data.stringTraceVisible = !this.stringTraceVisible;
            },
            toggleLogs() {
                this.$data.logsVisible = !this.logsVisible;
            }
        }
    }

</script>