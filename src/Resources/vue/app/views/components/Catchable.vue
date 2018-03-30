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

        <h6 v-if="catchable.statusCode">
            <span :class="catchable.statusCode >= 200 && catchable.statusCode < 300 ? 'badge badge-info' : 'badge badge-danger'">
                {{ catchable.statusCode }} {{ catchable.statusCodeText }}
            </span>
        </h6>

        <div class="form-group" v-if="catchable.headers">
            <h5>Headers</h5>
            <ul>
                <li v-for="(value, header) in catchable.headers">
                    <span class="badge badge-info">{{ header }}</span> {{ value }}
                </li>
            </ul>
        </div>

        <div class="btn-group">
            <button @click="toggleMode('symfony')" :class="getButtonClass('symfony')">
                Symfony
            </button>
            <button @click="toggleMode('custom')" :class="getButtonClass('custom')">
                Custom
            </button>
            <button @click="toggleMode('string')" :class="getButtonClass('string')">
                String
            </button>
        </div>

        <br/>
        <br/>

        <div @dblclick="toggleStringTrace" v-if="'custom' == mode">
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

        <div v-if="'string' == mode">
            <p class="card-text new-line">{{ catchable.stackTraceString }}</p>
        </div>

        <div v-if="'symfony' == mode">
            <div class="stacktrace" v-if="catchable.trace.length">
                <!-- This sort of became unnecessary since we have the class and message above -->
                <!--<p style="font-size: 13px; margin: 0 0 5px 0;">-->
                    <!--{{ catchable.class }}: {{ catchable.message ? '- ' + catchable.message : '' }}-->
                <!--</p>-->

                <p v-for="trace in catchable.trace" style="font-size: 13px; margin: 0 0 5px 0;">
                    <template v-if="trace.file && trace.line && trace.function">
                        at {{ trace.class + trace.type + trace.function }}
                        (<span v-html="trace.args_formatted"></span>)
                        <br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(<span v-html="trace.file_formatted"></span>)
                    </template>
                    <template v-else-if="trace.file && trace.line">
                        at (<span v-html="trace.file_formatted"></span>)
                    </template>
                </p>
            </div>
        </div>

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
                mode: 'symfony' // symfony, custom, string
            };
        },
        mounted() {
          console.log(this.catchable);
          console.log(this.catchable.headers);
          console.log(this.catchable.headers.length);
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
            toggleMode(mode) {
              this.mode = mode;
            },
            getButtonClass(mode) {
                if (mode == this.mode) {
                    return 'btn btn-primary';
                }

                return 'btn btn-default';
            },
            togglePrevious() {
                this.$data.previousVisible = !this.previousVisible;
            },
            toggleLogs() {
                this.$data.logsVisible = !this.logsVisible;
            }
        }
    }

</script>