import catchables from './modules/catchables'
import createLogger from '../utilities/Logger'

const debug = process.env.NODE_ENV !== 'production'

export default new Vuex.Store({
    modules: {
        catchables
    },
    strict: debug,
    plugins: debug ? [createLogger()] : []
})