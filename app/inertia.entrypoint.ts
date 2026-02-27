import '../app/main.entrypoint.css'
import 'tippy.js/dist/tippy.css';
import 'tippy.js/themes/light-border.css';
import 'tippy.js/themes/translucent.css';
import 'tippy.js/animations/shift-away-subtle.css';

import { createInertiaApp } from '@inertiajs/vue3'
import { createApp, DefineComponent, h, nextTick } from 'vue';
import { plugin as VueTippy } from 'vue-tippy';
import { createPinia } from 'pinia';
import { useTooltipStore } from '@/stores/tooltip';

import { usePage } from '@inertiajs/vue3';

const page = usePage();

const pinia = createPinia();

let tooltipStore: any = null;

const tippyOptions = {
    directive: 'tippy',
    defaultProps: {
        placement: 'right',
        arrow: false,
        theme: 'spiritvale',
        allowHTML: true,
        animation: 'shift-away-subtle',
        delay: [100, 0],
        inertia: true,
        followCursor: true,
        maxWidth: 550,
        interactive: false, //TODO: check
        onTrigger: async (instance: any, event: any) => {
            if (instance.props.contentLazy) {
                if (tooltipStore === null) {
                    tooltipStore = useTooltipStore();
                }
                if (tooltipStore) {
                    if (instance.props.contentLazy.slug) {
                        const gameData = page.props.gameData[instance.props.contentLazy.type].find(m => m.Slug === instance.props.contentLazy.slug);
                        if (gameData === undefined || gameData === null) {
                            console.error('gameData not found: ' + instance.props.contentLazy.type + ' ' + instance.props.contentLazy.slug);
                        } else {
                            tooltipStore.type = instance.props.contentLazy.type;
                            tooltipStore.data = gameData;
                        }
                    } else {
                        console.error('legacy');
                        tooltipStore.type = instance.props.contentLazy.type;
                        tooltipStore.data = instance.props.contentLazy.data;
                    }

                    await nextTick();

                    const container = document.getElementById('tooltip-container-lazy');
                    if (container) {
                        instance.setContent(container.innerHTML);
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            }
        },
    },
};

void createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob<DefineComponent>('./Pages/**/*.vue');
        return pages[`./Pages/${name}.vue`]();
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(pinia)
            .use(VueTippy, tippyOptions)
            .mount(el)
    },
})
