<script>
import Feature from "../dashboard-components/Feature.vue";
import AppHeader from "../dashboard-components/AppHeader.vue";
import AppSidebar from "../dashboard-components/AppSidebar.vue";
import { useUser } from "../composables/useUser.js";
import Utils from "../../Utils.js";
import { onMounted, ref } from "vue";
import pic from "../../../../public/images/kike-logo.png";
import axios from "../../axios.js";
import Swal from "sweetalert2";

const isSidebarVisible = ref(false); // Ref to control sidebar visibility

const toggleSidebar = function () {
    isSidebarVisible.value = !isSidebarVisible.value; // Toggle visibility
};

export default {
    name: 'Conversation',
    components: { Feature, AppHeader, AppSidebar },
    data() {
        return {
            error: '',
            loading: false,
            newMessage: '',  // Track input for new message
        };
    },
    methods: {
        async sendMessage() {
            if (this.newMessage.trim() === '') return; // Don't send empty messages
            try {
                const response = await axios.post('/message', {
                    message: this.newMessage,
                    customer_id: new URLSearchParams(window.location.search).get('id')
                });
                await this.handleResponse(response, "Sent");
            } catch (error) {
                this.handleError(error);
            } finally {
                this.loading = false;
                this.newMessage = ''; // Clear input field after sending
            }
        },

        async stopMessage() {
            try {
                const response = await axios.post('/stop-ai', {
                    customer_id: new URLSearchParams(window.location.search).get('id')
                });

                await this.handleResponse(response, "Successfully Stopped KIKO AI");
            } catch (error) {
                this.handleError(error);
            } finally {
                this.loading = false;
            }
        },
    },

    setup() {
        const { loadUser, allConversation, loadAllConversation, user, loading, error } = useUser();
        const utils = new Utils();

        const id = ref('');
        const phone = ref('');
        const name = ref('');

        const handleResponse = async (response, text) => {
            if ([200, 201].includes(response.status)) {
                await Swal.fire({
                    title: 'Success!',
                    text: text,
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
                loadAllConversation(id.value);
            } else {
                error.value = "Token is missing in the URL.";
            }
        });

        return { user, phone, name, utils, allConversation, loading, error, pic, id, isSidebarVisible, toggleSidebar, handleResponse, handleError };
    }
};
</script>

<template>
    <div class="dashboard">
        <AppSidebar :toggleSidebar="toggleSidebar" :isSidebarVisible="isSidebarVisible" :data="user" />
        <main>
            <AppHeader text="Dashboard" :data="user" :toggleSidebar="toggleSidebar" :isSidebarVisible="isSidebarVisible" />

            <div class="box-container">
                <div class="chat-room">
                    <div v-if="loading" class="loading">Loading...</div>
                    <div v-if="error" class="error">{{ error }}</div>

                    <div class="messages-container" v-if="!error">
                        <div
                            v-for="(message, index) in allConversation"
                            :key="index"
                            :class="message.status === 'sent' ? 'message-sent' : 'message-received'"
                        >
                            <div
                                :class="message.status === 'sent' ? 'message sent' : 'message received'">
                                <div v-if="message.status === 'sent'" class="message-user">+{{ phone }}</div>
                                <div class="message-text">{{ message.message }}</div>
                                <div class="message-time">{{ new Date(message.created_at).toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'}) }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Send Message Input -->
                    <div class="message-input-container">
                        <textarea
                            rows="4"
                            v-model="newMessage"
                            class="texta"
                            placeholder="Type a message..."
                            @keyup.enter="sendMessage"
                        ></textarea>
                        <button class="btn-chat" @click="sendMessage">Send</button>
                        <button type="button" class="btn-chat stop-btn" @click="stopMessage">Stop</button>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>


<style scoped>
.texta {
    border: 1px solid #ddd;
    margin: 5px;
    width: 100%;
    padding: 10px;
    font-size: 1rem;
    border-radius: 8px;
    resize: none;
}

.box-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 80%;
    margin-top: 20px;
    flex-direction: column; /* For mobile responsiveness */
}

.chat-room {
    width: 90%; /* Adjusted to be responsive */
    height: 80%;
    display: flex;
    flex-direction: column;
    padding: 10px;
}

.messages-container {
    flex-grow: 1;
    overflow-y: auto;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    max-height: 60vh; /* Prevent long chat overflow on small screens */
}

.message {
    display: flex;
    flex-direction: column;
    padding: 10px;
    margin-bottom: 10px;
    max-width: 75%;
    border-radius: 15px;
    position: relative;
    font-family: 'Arial', sans-serif;
    word-wrap: break-word;
}
.message-received{
    display: flex;
    justify-content: flex-end;
}
.message-sent{
    display: flex;
    justify-content: flex-start;
}
.sent {
    justify-content: flex-start;
    align-self: flex-end;
    background-color: #e1f5fe;
    text-align: right;
}

.sent .message-text {
    background-color: #007bff;
    color: white;
}

/* Align user messages (received) to the left */
.received {
    justify-content: flex-end;
    align-self: flex-start;
    background-color: #ffffff;
    text-align: left;
}

.received .message-text {
    background-color: #f0f0f0;
    color: #333;
}

.message-user {
    font-weight: bold;
    color: #007bff;
    margin-bottom: 5px;
    font-size: 0.75rem;
}

.message-text {
    padding: 10px 15px;
    border-radius: 15px;
    font-size: 0.875rem;
    line-height: 1.5;
    color: #333;
}

.message-time {
    font-size: 0.75rem;
    color: #888;
    margin-top: 8px;
    align-self: flex-end;
}

/* Input container */
.message-input-container {
    display: flex;
    align-items: center;
    padding: 10px 0;
    border-top: 1px solid #ddd;
    background-color: #ffffff;
    flex-wrap: wrap; /* Allow buttons to stack on small screens */
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
    margin-left: 10px;
}

.message-input-container button:hover {
    background-color: #0056b3;
}

.stop-btn {
    background-color: #ff5c5c;
}

.stop-btn:hover {
    background-color: #e04f4f;
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

/* Mobile responsiveness */
@media (max-width: 768px) {
    .chat-room {
        width: 95%; /* Slightly reduce width on mobile */
    }

    .messages-container {
        padding: 15px;
    }

    .message-text {
        font-size: 0.9rem; /* Slightly reduce font size for smaller screens */
    }

    .message-input-container {
        flex-direction: column; /* Stack buttons and text area */
    }

    .texta {
        width: 100%; /* Full width text input */
        margin-bottom: 10px;
    }

    .message-input-container button {
        width: 100%; /* Full width buttons on mobile */
        margin-left: 0;
        margin-top: 10px; /* Add space between buttons */
    }
}

@media (max-width: 480px) {
    .box-container {
        margin-top: 10px;
    }

    .message-user {
        font-size: 0.7rem;
    }

    .message-text {
        font-size: 0.85rem; /* Reduce text size even more */
    }

    .message-time {
        font-size: 0.7rem;
    }

    .message-input-container button {
        font-size: 0.9rem; /* Smaller button text on small screens */
    }

    .message-input-container {
        padding: 8px 0; /* Less padding on mobile */
    }
}
</style>
