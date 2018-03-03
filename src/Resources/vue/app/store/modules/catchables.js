import * as types from '../mutation-types'
import {deepCopy} from '../../utilities/Util'

Array.prototype.findOneBy = function (prop, val) {
    let index = this.map((e) => {
        return e[prop];
    }).indexOf(val);

    return this[index];
};
Array.prototype.findIndexBy = function (prop, val) {
    return this.map((e) => {
        return e[prop];
    }).indexOf(val);
};

function getInitialFilter() {
    return {
        message: null,
        file: null,
        class: null
    };
}

// initial state
const state = {
    all: [],
    currentRecord: null,
    total: null,
    currentPage: 1,
    recordsPerPage: 10,
    filter: getInitialFilter(),
    fetchAllInProgress: false,
    fetchInProgress: false
};

// getters
const getters = {
    getAll: state => state.all,
    getCurrentRecord: state => state.currentRecord,
    getById: state => {
        return (id) => state.all.findOneBy('id', id);
    },
    getFilter: state => state.filter,
    total: state => state.total,
    // keep if below for speed optimization
    pageCount: state => !state.total ? 0 : Math.ceil(state.total / state.recordsPerPage),
    currentPage: state => state.currentPage,
    getFetchAllInProgress: state => state.fetchAllInProgress,
    getFetchInProgress: state => state.fetchInProgress
};

// actions
const actions = {
    updateFilter({commit}, data) {
        commit(types.CATCHABLE_FILTER_UPDATE, {
            key: data.key,
            value: data.value
        });
    },
    loadCatchables({commit}, data) {
        return new Promise((resolve, reject) => {

            commit(types.CATCHABLE_SET_LIST_IN_PROGRESS, {
                value: true
            });

            if (!data) {
                data = {
                    page: 1,
                };
            }

            let params = {
                page: data.page,
                limit: state.recordsPerPage
            };

            params = Object.assign({}, params, state.filter);

            axios.get(config.urls.list, {params: params})
                .then(response => {
                    if (!response.data.success) {
                        reject(response.data.message);
                    }

                    commit(types.CATCHABLE_LIST, {
                        entities: response.data.entities,
                        total: response.data.total,
                        page: data.page
                    });
                    commit(types.CATCHABLE_SET_LIST_IN_PROGRESS, {
                        value: false
                    });
                    resolve();
                })
                .catch(function (error) {
                    commit(types.CATCHABLE_SET_LIST_IN_PROGRESS, {
                        value: false
                    });
                    reject(error);
                });
        });
    },
    getCatchable({commit}, data) {
        return new Promise((resolve, reject) => {

            let current = state.all.findOneBy('id', data.id);
            if (current && (!data.fresh)) {
                commit(types.CATCHABLE_SET_CURRENT, {
                    entity: current
                });

                resolve(current);
            }

            commit(types.CATCHABLE_SET_GET_IN_PROGRESS, {
                value: true
            });

            let url = config.urls.get.replace('-id-', data.id);

            axios.get(url)
                .then(response => {
                    if (!response.data.success) {
                        reject(response.data.message);
                    }

                    commit(types.CATCHABLE_SET_CURRENT, {
                        entity: response.data.entity
                    });
                    commit(types.CATCHABLE_SET_GET_IN_PROGRESS, {
                        value: false
                    });
                    resolve();
                })
                .catch(function (error) {
                    commit(types.CATCHABLE_SET_GET_IN_PROGRESS, {
                        value: false
                    });
                    reject(error);
                });

        });
    },
    deleteCatchable({commit}, data) {
        return new Promise((resolve, reject) => {

            let url = config.urls.delete.replace('-id-', data.id);

            axios.delete(url)
                .then(response => {
                    if (!response.data.success) {
                        reject(response.data.message);
                    }

                    commit(types.CATCHABLE_DELETE, {
                        id: data.id
                    });
                    resolve();
                })
                .catch(function (error) {
                    reject(error);
                });

        });
    },
    deleteAll({commit}) {
        return new Promise((resolve, reject) => {

            axios.delete(config.urls.deleteAll)
                .then(response => {
                    if (!response.data.success) {
                        reject(response.data.message);
                    }

                    commit(types.CATCHABLE_DELETE_ALL);
                    resolve();
                })
                .catch(function (error) {
                    reject(error);
                });

        });
    },
};

// mutations
const mutations = {
    [types.CATCHABLE_LIST](state, {entities, total, page}) {
        state.all = entities;
        state.total = total;
        state.currentPage = page;
    },
    [types.CATCHABLE_SET_CURRENT](state, {entity}) {
        state.currentRecord = deepCopy(entity);
    },
    [types.CATCHABLE_FILTER_UPDATE](state, {key, value}) {
        let oldFilter = deepCopy(state.filter);
        oldFilter[key] = value;
        state.filter = oldFilter;
    },
    [types.CATCHABLE_DELETE](state, {id}) {
        let index = state.all.findIndexBy('id', id);
        if (index >= 0) {
            state.all = state.all.filter(function (item) {
                return id !== item.id;
            });
        }
    },
    [types.CATCHABLE_DELETE_ALL](state) {
        state.all = [];
        state.total = 0;
    },
    [types.CATCHABLE_SET_LIST_IN_PROGRESS](state, {value}) {
        state.fetchAllInProgress = value;
    },
    [types.CATCHABLE_SET_GET_IN_PROGRESS](state, {value}) {
        state.fetchInProgress = value;
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};