
import { defineStore } from 'pinia';
import {fetchUser,fetchAllUser,fetchAllCustomers} from "./components/services/userService.js";

export const useUserStore = defineStore('user', {
    state: () => ({
        user: {email:"",name:"",phone:"",token:"",status:"",role:"",created_at:""},
        allUsers:[],
        allCustomer:[],
        loading: false,
        error: null,
    }),
    actions: {
        async loadUser(token) {
            this.loading = true;
            this.error = null;
            try {
                const response = await fetchUser(token);
                this.user = response.data.message;
                console.log(response.data.message)
            } catch (err) {
                this.error = 'Failed to load user data.';
                console.error(err);
            } finally {
                this.loading = false;
            }
        },
        async loadAllUser(token){
            this.loading = true;
            this.error = null;
            try {
                const response = await fetchAllUser(token);
                this.allUsers = response.data.message; // Adjust as needed based on your API response
                console.log(response.data.message)
            } catch (err) {
                this.error = 'Failed to load user data.';
                console.error(err);
            } finally {
                this.loading = false;
            }
        },
        async loadAllCustomers(token){
            this.loading = true;
            this.error = null;
            try {
                const response = await fetchAllCustomers(token);
                this.allCustomer = response.data.message; // Adjust as needed based on your API response
                console.log(response.data.message)
                console.log(response)
            } catch (err) {
                this.error = 'Failed to load user data.';
                console.error(err);
            } finally {
                this.loading = false;
            }
        }
    },
});
