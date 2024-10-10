
import { defineStore } from 'pinia';
import {fetchUser} from "./components/services/userService.js";

export const useUserStore = defineStore('user', {
    state: () => ({
        user: {email:"",name:"",phone:"",token:"",status:"",role:""},
        loading: false,
        error: null,
    }),
    actions: {
        async loadUser(token) {
            this.loading = true;
            this.error = null;
            try {
                const response = await fetchUser(token);
                this.user = response.data.message; // Adjust as needed based on your API response
                console.log(response.data.message)
            } catch (err) {
                this.error = 'Failed to load user data.';
                console.error(err);
            } finally {
                this.loading = false;
            }
        },
    },
});
