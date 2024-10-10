
import { createApp } from 'vue';
import router from "./router.js";
import App from "./components/App.vue";
import { createPinia } from 'pinia';
const app = createApp(App);
app.use(router);
const pinia = createPinia();

app.use(pinia);
app.mount('#app');
