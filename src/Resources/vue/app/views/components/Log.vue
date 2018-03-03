<template>

    <li class="list-group-item">
        <div>
            <p>
                <span :class="'badge badge-' + logClass">{{ log.level_name }}</span>
                {{ messageString }}
            </p>
            <p>
                <span class="badge badge-info">Date</span>
                {{ dateString }}
                <span class="badge badge-info">Channel</span>
                {{ log.channel }}
                <span class="badge badge-info">Context</span>
                {{ log.context }}
            </p>
        </div>
    </li>

</template>

<script>

    export default {
        props: ['log'],
        computed: {
            logClass() {
                // some funny js here: must toString string before switch (works like ===).
                let level = this.log.level_name.toLowerCase().toString();
                switch (level) {
                    case 'info':
                        return 'primary';
                    case 'debug':
                    case 'notice':
                        return 'info';
                    case 'critical':
                    case 'error':
                    case 'alert':
                    case 'emergency':
                        return 'danger';
                    case 'warning':
                        return 'warning';
                }
            },
            dateString() {
                let date = new Date(this.log.timestamp * 1000);
                return date.toLocaleString();
            },
            messageString() {
                if (this.log.context.length < 1) {
                    return this.log.message;
                }

                let message = this.log.message;
                for (let k in this.log.context) {
                    if (this.log.context.hasOwnProperty(k)) {
                        message = message.replace('{' + k + '}', this.log.context[k]);
                    }
                }

                return message;
            }
        }
    }

</script>