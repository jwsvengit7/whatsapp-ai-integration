
import { defineStore } from 'pinia';
import {fetchUser, fetchAllUser, fetchAllCustomers, fetchAllProduct} from "./components/services/userService.js";

export const useUserStore = defineStore('user', {
    state: () => ({
        user: {email:"",name:"",phone:"",token:"",status:"",role:"",created_at:"",image:""},
        allUsers:[],
        allCustomer:[],
        allProduct:[],
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

            } catch (err) {
                this.error = 'Check your internet connections';
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
                this.allUsers = response.data.message;
            } catch (err) {
                this.error = 'Check your internet connections';
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
                this.allCustomer = response.data.message;

            } catch (err) {
                this.error = 'Check your internet connections';
                console.error(err);
            } finally {
                this.loading = false;
            }
        },
        async loadAllProduct(token){
            this.loading = true;
            this.error = null;
            try {
                const response = await fetchAllProduct(token);
                this.allProduct = response.data.message;

            } catch (err) {
                this.error = 'Check your internet connections';
                console.error(err);
            } finally {
                this.loading = false;
            }
        }
    },
});
