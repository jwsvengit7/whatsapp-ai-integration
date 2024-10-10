import { useUserStore } from "../../store.js";
import { computed, ref } from 'vue';

export const useUser = () => {
    const store = useUserStore();
    const loading = ref(false); // Local loading state

    const loadUser = async () => {
        loading.value = true; // Set loading to true
        const token = localStorage.getItem("token");
        await store.loadUser(token);
        loading.value = false; // Set loading to false after loading
        console.log("User data loaded:", store.user);
    };

    const user = computed(() => store.user);
    const error = computed(() => store.error);

    return { loadUser, user, loading, error };
};
