import LoggerViewer from './components/LoggerViewer.vue';

Statamic.booting(() => {
    Statamic.$components.register('logger-viewer', LoggerViewer);
});