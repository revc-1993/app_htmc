import "../css/main.css";

import { createPinia } from "pinia";
import { useStyleStore } from "@/Stores/style.js";

import { darkModeKey, styleKey } from "@/config.js";

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy/dist/vue.m";

import "vue-toastification/dist/index.css";
import "vue-select/dist/vue-select.css";
import "floating-vue/dist/style.css";

import Toast from "vue-toastification";
import vSelect from "vue-select";
import FloatingVue from "floating-vue";

const appName =
    window.document.getElementsByTagName("title")[0]?.innerText || "Laravel";

const pinia = createPinia();

createInertiaApp({
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    title: (title) => `${title} - ${appName}`,
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(pinia)
            .use(ZiggyVue, Ziggy)
            .use(Toast, {
                transition: "Vue-Toastification__fade",
            })
            .use(FloatingVue)
            .component("v-select", vSelect)
            .mount(el);
    },
    progress: {
        color: "#046ff1",
    },
});

const styleStore = useStyleStore(pinia);

/* App style */
styleStore.setStyle(localStorage[styleKey] ?? "basic");

/* Dark mode */
if (
    (!localStorage[darkModeKey] &&
        window.matchMedia("(prefers-color-scheme: dark)").matches) ||
    localStorage[darkModeKey] === "1"
) {
    styleStore.setDarkMode(false);
}
