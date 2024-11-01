<template>
    <div class="dashboard">
        <AppSidebar :data="user" />
        <main>
            <AppHeader text="Settings" :data="user" />
            <div class="box-container">
                <Preloader :loading="loadings" />
                <div class="div1">
                <form class="add-container" @submit.prevent="createNewAdmin">
                    <h2 class="form-title">Manage Products</h2>
                    <div class="container-box">
                        <h3 class="section-title">Select Product</h3>
                        <div class="button-group">
                            <div v-for="(item, index) in allProduct" :key="index">
                                <button type="button" :value="item.name" @click="selectProduct(item.name)" class="product-button">{{ item.name }}</button>
                            </div>
                        </div>
                        <p class="selected-product">{{ type }}</p>

                        <div v-if="type">
                            <label :for="type" class="input-label">Edit Questions for {{ type }}</label>
                            <input type="text" :name="type" :value="type" class="text-input" readonly />
                            <div v-for="(product, index) in allProduct" :key="index">
                                <div v-if="product.name === type && product.questions?.length">
                                    <ol class="question-list">
                                        <input type="hidden" name="id" :value="id" />
                                        <li v-for="(question, qIndex) in product.questions" :key="qIndex" class="question-item">
                                            <div style="display: flex;">
                                                <input type="text" v-model="question.question" class="question-input" />
                                                <button type="button" @click="confirmDeleteQuestion(product, qIndex)" class="delete-button">Delete</button>
                                            </div>
                                        </li>
                                    </ol>
                                </div>
                            </div>

                            <div class="new-question-container">
                                <input type="text" v-model="newQuestion" placeholder="Add a new question" class="text-input new-question-input" />
                                <button type="button" @click="addQuestion" class="add-question-button">Add Question</button>
                            </div>
                        </div>

                        <!-- Advanced Settings Toggle -->
                        <div class="advanced-settings">
                            <button type="button" @click="showAdvanced = !showAdvanced" class="advanced-settings-button">
                                {{ showAdvanced ? "Hide Advanced Settings" : "Show Advanced Settings" }}
                            </button>

                            <!-- Advanced Settings Section -->
                            <div v-if="showAdvanced" class="advanced-settings-content">
                                <h3 class="section-title">Schedule a Message</h3>
                                <label for="scheduledMessage" class="input-label">Message</label>
                                <input type="text" v-model="scheduledMessage" placeholder="Enter the message to schedule" class="text-input" />

                                <label for="scheduledTime" class="input-label">Schedule Time</label>
                                <input type="datetime-local" v-model="scheduledTime" class="text-input" />
                                <button type="button" @click="add" class="add-question-button">Add Question</button>
                            </div>
                        </div>
                    </div>

                    <div class="container-box">
                        <div class="submit-container">
                            <button :disabled="loadings" class="submit-button">Save</button>
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </main>
    </div>
</template>
<style scoped>
.div1{
    width:auto;
    height: 700px;
    overflow: scroll;
}
.dashboard {
    display: flex;
    min-height: 100vh;
    background: #f5f7fa;
}

.add-container {
    width: 100%;
    height: auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
}

.form-title {
    margin-bottom: 20px;
    font-size: 24px;
    color: #333;
}

.container-box {
    width: 80%;
    margin-top: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 20px;
}

.section-title {
    font-size: 20px;
    margin-bottom: 10px;
    color: #007bff;
}

.button-group {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.product-button {
    padding: 10px 15px;
    background: #007bff;
    border: none;
    color: white;
    cursor: pointer;
    border-radius: 5px;
    transition: background 0.3s;
}

.product-button:hover {
    background: #0056b3;
}

.selected-product {
    font-weight: bold;
    margin-top: 10px;
}

.input-label {
    margin: 10px 0;
    font-weight: bold;
    color: #555;
}

.text-input {
    width: 100%;
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #ccc;
    background: #f8f8f8;
    margin-bottom: 15px;
}

.question-list {
    list-style-type: none;
    padding: 0;
}

.question-item {
    margin-top: 10px;
}

.question-input {
    width: 100%;
    padding: 8px;
    border-radius: 5px;
    border: 1px solid #ccc;
    background: #f8f8f8;
}

.new-question-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.new-question-input {
    width: 80%;
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #ccc;
    background: #f8f8f8;
}

.add-question-button {
    padding: 10px 15px;
    background: #28a745;
    border: none;
    color: white;
    cursor: pointer;
    border-radius: 5px;
    transition: background 0.3s;
}

.add-question-button:hover {
    background: #218838;
}

.submit-container {
    display: flex;
    justify-content: flex-end;
}

.submit-button {
    padding: 10px 20px;
    background: #007bff;
    border: none;
    color: white;
    cursor: pointer;
    border-radius: 5px;
    transition: background 0.3s;
}

.submit-button:disabled {
    background: #ccc;
    cursor: not-allowed;
}

.submit-button:hover:not(:disabled) {
    background: #0056b3;
}

.delete-button{
    background: #007bff;
    border: 0 none;
    color:white;
    padding: 10px;
}

.advanced-settings {
    margin-top: 20px;
}

.advanced-settings-button {
    padding: 10px 15px;
    background: #17a2b8;
    border: none;
    color: white;
    cursor: pointer;
    border-radius: 5px;
    transition: background 0.3s;
}

.advanced-settings-button:hover {
    background: #138496;
}

.advanced-settings-content {
    margin-top: 20px;
}
</style>

<script>
import AppSidebar from "../dashboard-components/AppSidebar.vue";
import AppHeader from "../dashboard-components/AppHeader.vue";
import Preloader from "../tools/preloader.vue";
import { onMounted, ref, watch } from "vue";
import { useUser } from "../composables/useUser.js";
import axios from "../../axios.js";
import Swal from "sweetalert2";
import { useRouter } from "vue-router";

export default {
    name: 'EducateAI',
    components: { Preloader, AppHeader, AppSidebar },

    data() {
        return {
            type: '',
            newQuestion: '',
            showAdvanced: false,
            scheduledMessage: '',
            scheduledTime: ''
        };
    },
    mounted() {
        this.setTypeFromQuery();
    },
    watch: {
        '$route.query.name': function (newType) {
            this.type = newType || 'Fuel';
        }
    },
    methods: {
        setTypeFromQuery() {
            this.type = this.$route.query.name || 'Fuel';
        },


    },
    setup() {
        const router = useRouter();
        const { loadUser, user, allProduct, loadAllProduct } = useUser();

        const type = ref('');
        const newQuestion = ref('');
        const loadings = ref(false);
        const errors = ref('');
        const id = ref('');
        const scheduledMessage = ref('');
        const scheduledTime = ref('');
        onMounted(() => {
            loadUser();
            loadAllProduct();
        });

        const selectProduct = (productName) => {
            type.value = productName;
            const selectedProduct = allProduct.value.find(product => product.name === productName);

            id.value = selectedProduct ? selectedProduct.id : null;
            router.push({ query: { name: type.value } });
        };
        const addQuestion = () => {
            if (!newQuestion.value.trim()) return;

            const currentProduct = allProduct.value.find(product => product.name === type.value);
            if (currentProduct) {
                currentProduct.questions.push({ question: newQuestion.value });

                newQuestion.value = '';
            }
        };
        const confirmDeleteQuestion = (product, index) => {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteQuestion(product, index);
                }
            });
        };

        const deleteQuestion = (product, index) => {
            const questionToDelete = product.questions[index];

            const questionIndex = product.questions.indexOf(questionToDelete);
            if (questionIndex > -1) {
                product.questions.splice(questionIndex, 1);
            }
        };

        const createNewAdmin = async () => {
            loadings.value = true;
            errors.value = '';
            const currentProduct = allProduct.value.find(product => product.name === type.value);

            const data = {
                type: type.value,
                id: currentProduct ? currentProduct.id : id.value,
                questions: currentProduct ? currentProduct.questions : []
            };

            try {
                const response = await axios.put('/update-product', data);
                await handleResponse(response);


            } catch (error) {
                handleError(error);
            } finally {
                loadings.value = false;
            }
        };
        const add =  async () => {
            const datas ={
                message_content: scheduledMessage.value,
                scheduled_date: scheduledTime.value,
                product_name:id.value

            };
            console.log(datas)
            const data =   await axios.post('/schedule-message', datas);
            console.log(data)
            await handleResponse(data);

        }

        const handleResponse = async (response) => {
            if ([200, 201].includes(response.status)) {
                await Swal.fire({
                    title: 'Success!',
                    text: 'Product update successfully',
                    icon: 'success',
                    confirmButtonText: 'Okay'
                });
                await router.push('/dashboard/all-product');
            } else {
                await showError('An unexpected error occurred.');
            }
        };

        const handleError = (error) => {
            const errorMessage = error.response?.data?.message || 'An error occurred. Please try again.';
            errors.value = errorMessage;
            showError(errors.value);
        };

        const showError = async (message) => {
            await Swal.fire({
                title: 'Error!',
                text: message,
                icon: 'error',
                confirmButtonText: 'Okay'
            });
        };

        return {
            user,
            type,
            newQuestion,
            loadings,
            errors,
            id,
            addQuestion,
            confirmDeleteQuestion,
            allProduct,
            selectProduct,
            scheduledMessage,
            scheduledTime,
            createNewAdmin,
            add
        };
    }
};
</script>
