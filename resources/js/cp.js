import StatamicLoggerViewer from './components/viewer.vue';

Statamic.booting(() => {
    Statamic.$components.register('statamic-logger-viewer', StatamicLoggerViewer);
});