import { useUserStore } from "../../store.js";
import { computed, ref } from 'vue';

export const useUser = () => {
    const store = useUserStore();
    const loading = ref(false); // Local loading state

    const loadUser = async () => {
        loading.value = true;
        const token = localStorage.getItem("token");
        await store.loadUser(token);
        loading.value = false;
        console.log("User data loaded:", store.user);
    };

    const loadAllUsers = async () => {
        loading.value = true;
        const token = localStorage.getItem("token");
        await store.loadAllUser(token);
        loading.value = false;
        console.log("All users data loaded:", store.allUsers);
    };

    const loadAllCustomers = async () => {
        loading.value = true;
        const token = localStorage.getItem("token");
        await store.loadAllCustomers(token);
        loading.value = false;
        console.log("All users data loaded:", store.allCustomer);
    };
    const loadAllProduct = async () => {
        loading.value = true;
        const token = localStorage.getItem("token");
        await store.loadAllProduct(token);
        loading.value = false;
        console.log("All users data loaded:", store.allProduct);
    };

    const user = computed(() => store.user);
    const allUsers = computed(() => store.allUsers);
    const allCustomers = computed(() => store.allCustomer);
    const allProduct = computed(() => store.allProduct);
    const error = computed(() => store.error);

    return { loadUser,loadAllUsers,loadAllCustomers,loadAllProduct, user,allUsers,allCustomers,allProduct, loading, error };
};
