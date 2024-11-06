<script >
import Feature from "../dashboard-components/Feature.vue";
import AppHeader from "../dashboard-components/AppHeader.vue";
import AppSidebar from "../dashboard-components/AppSidebar.vue";
import {useUser} from "../composables/useUser.js";
import Utils from "../../Utils.js";
import {onMounted, ref} from "vue";
import pic from "../../../../public/images/kike-logo.png";
import axios from "../../axios.js";
import Swal from "sweetalert2";
const isSidebarVisible = ref(false); // Ref to control sidebar visibility

const toggleSidebar = function () {
    isSidebarVisible.value = !isSidebarVisible.value; // Toggle visibility
};
export default {
    name: 'Conversation',
    components: {Feature, AppHeader, AppSidebar},
    data() {
        return {
            error: '',
            loading: false,
            newMessage: '',  // Track input for new message
        };
    },
    methods: {
        async sendMessage() {
            if (this.newMessage.trim() === '') return;
            try {
                const response = await axios.post('/message', {
                    message: this.newMessage,
                    customer_id: new URLSearchParams(window.location.search).get('id')
                });
                if (response.status === 200) {
                    this.newMessage = '';
                } else {
                    this.error = 'Failed to send message.';
                }
            } catch (error) {
                console.error('Error sending message:', error);
                this.error = 'Failed to send message. Please try again.';
            }
        },

        async stopMessage(){

            try {
                const response = await axios.post('/stop-ai', {
                    customer_id: new URLSearchParams(window.location.search).get('id')
                });

                await this.handleResponse(response);
            } catch (error) {
                this.handleError(error);
            } finally {
                this.loading.value = false;
            }
        },

    },


    setup() {
        const { loadUser, allConversation, loadAllConversation, user, loading, error } = useUser();
        const utils = new Utils();

        const id = ref('');
        const phone = ref('');
        const name = ref('');


        const handleResponse = async (response) => {
            if ([200, 201].includes(response.status)) {
                await Swal.fire({
                    title: 'Success!',
                    text: 'Product created successfully',
                    icon: 'success',
                    confirmButtonText: 'Okay'
                });

            } else {
                await showError('An unexpected error occurred.');
            }
        };

        const handleError = (error) => {
            const errorMessage = error.response?.data?.message || 'An error occurred. Please try again.';
            const validationErrors = error.response?.data?.errors || {};

            showError(Object.values(validationErrors).flat()[0] || errorMessage);
        };

        const showError = async (message) => {
            await Swal.fire({
                title: 'Error!',
                text: message,
                icon: 'error',
                confirmButtonText: 'Okay'
            });
        };

        onMounted(() => {
            loadUser();
            id.value = new URLSearchParams(window.location.search).get('id');
            phone.value = new URLSearchParams(window.location.search).get('phone');
            name.value = new URLSearchParams(window.location.search).get('name');
            if (id.value) {
                console.log(id.value)
                loadAllConversation(id.value);
            } else {
                error.value = "Token is missing in the URL.";
            }
        });

        return { user,phone,name, utils, allConversation, loading, error, pic, id,isSidebarVisible,toggleSidebar,handleResponse ,handleError};
    }
}
</script>

<template>
    <div class="dashboard">
        <AppSidebar :toggleSidebar="toggleSidebar"  :isSidebarVisible="isSidebarVisible" :data="user"></AppSidebar>
        <main>
            <AppHeader text="Dashboard" :data="user" :toggleSidebar="toggleSidebar" :isSidebarVisible="isSidebarVisible"></AppHeader>

            <div class="box-container">
                <div class="chat-room">
                    <div v-if="loading" class="loading">Loading...</div>

                    <div v-if="error" class="error">{{ error }}</div>

                    <div class="messages-container"  v-if="!error" >
                        <div
                            v-for="(message, index) in allConversation"
                            :key="index"
                            :class="message.status === 'sent' ? 'message-sent' : 'message-received'"
                        >
                            <div
                                :class="message.status === 'sent' ? 'message sent' : 'message received'">
                            <!-- Display sender's name for received messages only -->
                            <div v-if="message.status === 'sent'" class="message-user">+{{ phone }}</div>

                            <div class="message-text">
                                {{ message.message }}
                            </div>

                            <!-- Timestamp for both sent and received messages -->
                            <div class="message-time">{{ new Date(message.created_at).toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'}) }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Send Message Input -->
                    <div class="message-input-container">
                        <textarea
                            rows=4
                            cols="50"
                            v-model="newMessage"
                            class="texta"
                            placeholder="Type a message..."
                            @keyup.enter="sendMessage"
                        ></textarea>
                        <button class="btn-chat" @click="sendMessage">Send</button>
                        <button type="button" class="btn-chat" @click="stopMessage">Stop</button>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<style scoped>
.texta{
    border: 1px solid #ddd;
    margin: 5px;
}
.box-container{
    height: 80%;
}
.chat-room {
    display: flex;
    flex-direction: column;
    height: 90%;
    justify-content: center;
    align-items: center;
    padding: 10px;
}


.messages-container {
    flex-grow: 1;
    width: 70%;
    overflow-y: auto;
    padding: 20px;
    margin-bottom: 10px;
    background-color: #ffffff;
    border-radius: 10px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
}

.message {
    display: flex;
    flex-direction: column;
    padding: 10px;
    margin-bottom: 10px;
    max-width: 60%;
    border-radius: 20px;

    position: relative;
    font-family: 'Arial', sans-serif;
    word-wrap: break-word;
}

.message-user {
    font-weight: bold;
    color: #007bff;
    margin-bottom: 5px;
    font-size: 0.7rem;
}

.message-text {
    padding: 10px 12px;
    border-radius: 15px;
    font-size: 0.7rem;
    line-height: 1.2;
    color: #333;
}

.message-time {
    font-size: 0.75rem;
    color: #888;
    margin-top: 8px;
    align-self: flex-end;
}

/* Sent messages styling */
.sent {

    align-self: flex-end;
    background-color: #e6f7ff;
    text-align: right;
}

.sent .message-text {
    background-color: #007bff;
    color: white;
}

/* Received messages styling */
.received {
    align-self: flex-start;
    background-color: #f1f1f1;
    text-align: left;
}

.received .message-text {
    background-color: #f0f0f0;
    color: #333;
}

/* Input container */
.message-input-container {
    display: flex;
    align-items: center;
    padding: 10px 0;
    border-top: 1px solid #ddd;
    background-color: #ffffff;
}

.message-input-container input {
    flex-grow: 1;
    padding: 10px;
    border-radius: 25px;
    border: 1px solid #ddd;
    margin-right: 10px;
    outline: none;
    font-size: 1rem;
    background-color: #f9f9f9;
}

.message-input-container button {
    padding: 10px 20px;
    border-radius: 25px;
    background-color: #007bff;
    color: white;
    border: none;
    cursor: pointer;
    font-size: 1rem;
    font-weight: bold;
    transition: background-color 0.3s ease;
}

.message-input-container button:hover {
    background-color: #0056b3;
}

/* Scrollbar styling for the chat container */
.messages-container {
    scrollbar-width: thin;
    scrollbar-color: #cccccc #ffffff;
}

.messages-container::-webkit-scrollbar {
    width: 8px;
}

.messages-container::-webkit-scrollbar-thumb {
    background-color: #cccccc;
    border-radius: 10px;
}
.message-sent{
    display: flex;
    justify-content: flex-start;
}
.message-received{
    display: flex;
    justify-content: flex-end;
}

</style>
