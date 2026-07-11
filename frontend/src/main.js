import { createApp } from 'vue'
import { createPinia } from 'pinia'
import PrimeVue from 'primevue/config'

import App from './App.vue'
import router from './router'
import './assets/main.css'
import { Toast } from 'primevue'

const app = createApp(App)

app.use(createPinia())
app.use(router)
app.use(PrimeVue)
app.use(Toast,{
    timeout:3000,
    position:'top-right',
    closeOnEscape:true,
    pauseOnHover:true,
})

app.mount('#app')
