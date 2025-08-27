<script setup>
import {Button, Checkbox, Listing, Select} from '@statamic/cms/ui';
import {computed, reactive, ref} from 'vue';
import LoggerAvatar from './Avatar.vue';

const props = defineProps({
    breadcrumbUrl: {type: String, required: true},
    dates: {type: Object, required: true},
    title: {type: String, required: true},
})

const dateOptions = computed(() => {
    let dates = JSON.parse(props.dates);

    let array = [];
    for (let date in dates) {
        array.push({
            label: dates[date],
            value: date
        });
    }

    return array;
})

const state = reactive({
    date: dateOptions.value.length ? dateOptions.value[0].value : null,
    showRaw: false,
    showUserFullDetails: false
})

const listing = ref(null);

const columns = [
    {field: 'date'},
    {field: 'user'},
    {field: 'type'},
    {field: 'detail'},
];

const listingParams = computed(() => {
    let params = {
        date: state.date
    };

    if (state.showRaw) {
        params.raw = true;
    }

    return params;
})

const download = computed(() => {
    return cp_url(`utilities/statamic-logger/download/` + state.date)
})


</script>
<template>
    <div id="logger-viewer">
        <header class="mb-6">

            <breadcrumb :title="__('Utilities')" :url="breadcrumbUrl"/>

            <div class="flex items-center">
                <h1 class="flex-1" v-text="title"/>
            </div>

            <div class="mt-6 sm:flex items-center sm:space-x-3">
                <div class="flex items-center space-x-3">
                    <div class="w-48">
                        <Select
                            v-model="state.date"
                            :options="dateOptions"
                            class="w-full"
                        />
                    </div>

                    <Button
                        v-if="state.date"
                        :href="download"
                        target="_blank">
                        Download
                    </Button>
                    <Button
                        v-else
                        :disabled="true"
                        target="_blank">
                        Download
                    </Button>
                </div>

                <div class="py-2 flex items-center space-x-4">
                    <Checkbox
                        v-model="state.showUserFullDetails"
                        label="Show full user details?"
                    />

                    <Checkbox
                        v-model="state.showRaw"
                        label="Show raw message?"
                    />
                </div>
            </div>

        </header>

        <Listing
            ref="listing"
            :additional-parameters="listingParams"
            :allow-customizing-columns="false"
            :allow-search="false"
            :columns="columns"
            :show-pagination-page-links="true"
            :show-pagination-per-page-selector="true"
            :sortable="false"
            :url="cp_url(`utilities/statamic-logger`)"
        >

            <template #cell-date="{ row }">
                {{ row.date }}
            </template>

            <template #cell-user="{ row }">
                <div class="flex gap-x-2">
                    <LoggerAvatar :user="row.user" class="size-8"></LoggerAvatar>
                    <div v-if="state.showUserFullDetails" style="margin-top:-1px;">
                        <div class="leading-tight">{{ row.user.name }}</div>
                        <div class="text-2xs text-gray-500">{{ row.user.id }}</div>
                    </div>
                </div>
            </template>

            <template #cell-type="{ row }">
                {{ row.type }}
            </template>

            <template #cell-detail="{ row }">
                <div v-html="row.detail"></div>
                <div v-if="state.showRaw" v-html="row.raw"></div>
            </template>

        </Listing>

    </div>
</template>
<style>

#logger-viewer td {
    vertical-align: top;
}

#logger-viewer td[data-column="date"],
#logger-viewer td[data-column="user"],
#logger-viewer td[data-column="type"] {
    width: 1%;
    white-space: nowrap;
}
</style>