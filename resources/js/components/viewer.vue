<template>
    <div id="logger-viewer">
        <header class="mb-6">

            <breadcrumb :url="breadcrumbUrl" :title="__('Utilities')"/>

            <div class="flex items-center">
                <h1 class="flex-1" v-text="title"/>
            </div>

            <div class="mt-6 sm:flex items-center sm:space-x-3">
                <div class="flex items-center space-x-3">
                    <div>
                        <select-input
                            v-model="date"
                            :placeholder="false"
                            :options="dateOptions"/>
                    </div>

                    <a :class="{
                           'pointer-events-none opacity-50 disabled': !date
                       }"
                       :href="download"
                       class="btn"
                       target="_blank">
                        Download
                    </a>
                </div>

                <div class="py-2 flex items-center space-x-4">
                    <label>
                        <input type="checkbox" v-model="userFullDetails"/>
                        Show full user details?
                    </label>

                    <label>
                        <input type="checkbox" v-model="raw"/>
                        Show raw message?
                    </label>
                </div>
            </div>

        </header>

        <div>
            <div class="card overflow-hidden p-0 relative">
                <div class="overflow-auto">
                    <table v-if="resource?.data"
                           class="data-table">
                        <thead>
                        <tr>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('User') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Details') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(row, index) in resource.data" :key="index">
                            <td class="whitespace-nowrap align-top" style="width: 1%;">
                                <div class="flex items-center h-10">{{ row.date }}</div>
                            </td>
                            <td class="whitespace-nowrap align-top" style="width: 1%;">
                                <div class="flex items-center h-10 space-x-2">
                                    <avatar class="w-8 h-8" :user="row.user"></avatar>
                                    <div v-if="userFullDetails">
                                        <div>{{ row.user.name }}</div>
                                        <div class="text-xs">{{ row.user.id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap align-top" style="width: 1%;">
                                <div class="flex items-center h-10">{{ row.type }}</div>
                            </td>
                            <td class=" align-top">
                                <div v-html="row.detail"></div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <data-list-pagination
            v-if="resource"
            class="mt-6"
            :resource-meta="resource"
            :per-page="perPage"
            :show-totals="true"
            @page-selected="selectPage"
            @per-page-changed="selectPerPage"/>
    </div>
</template>
<script>
export default {

    computed: {
        dateOptions() {
            let dates = JSON.parse(this.dates);

            let array = [];
            for (let date in dates) {
                array.push({
                    label: dates[date],
                    value: date
                });
            }

            return array;
        },

        download() {
            return cp_url(`utilities/statamic-logger/download/` + this.date);
        }
    },

    data() {
        return {
            date: null,
            entries: [],
            loading: true,
            requestUrl: cp_url(`utilities/statamic-logger`),
            page: 1,
            perPage: 25,

            raw: false,

            resource: null,

            userFullDetails: false,

            // cancel token
            _source: null
        }
    },

    methods: {

        request() {
            if (!this.date) {
                return;
            }

            this.loading = true;

            if (this.$data._source) this.$data._source.cancel();
            this.$data._source = this.$axios.CancelToken.source();

            let params = {
                date: this.date,
                page: this.page,
                perPage: this.perPage
            };

            if (this.raw) {
                params.raw = true;
            }

            this.$axios.get(this.requestUrl, {
                params: params,
                cancelToken: this.$data._source.token
            }).then(response => {
                this.resource = response.data;
            }).catch(e => {
                if (this.$axios.isCancel(e)) return;
                this.$toast.error(e.response ? e.response.data.message : __('Something went wrong'), {duration: null});
            }).finally(() => {
                this.loading = false;
            })
        },

        selectPage(page) {
            this.page = page;
            this.request();
        },

        selectPerPage(perPage) {
            this.perPage = perPage;
            this.selectPage(1);
        },

    },

    mounted() {
        // set the default date
        if (this.dateOptions.length > 0) {
            this.date = this.dateOptions[0].value;
        }
    },

    props: {
        breadcrumbUrl: {type: String, required: true},
        dates: {type: Object, required: true},
        title: {type: String, required: true},
    },

    watch: {
        date(newDate, oldDate) {
            if (newDate !== oldDate) {
                // reset page to 1
                this.page = 1;

                // load
                this.request();
            }
        },
        raw(newRaw, oldRaw) {
            if (newRaw != oldRaw) {
                this.request()
            }
        }
    }
}
</script>
<style>
#logger-viewer .space-x-4 > * + * {
    margin-left: 1rem
}
</style>