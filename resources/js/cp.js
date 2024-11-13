import MityLoggerViewer from './components/viewer.vue';

Statamic.booting(() => {
    Statamic.$components.register('mity-logger-viewer', MityLoggerViewer);
});