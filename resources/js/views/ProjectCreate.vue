<template>
  <div>
    <Navbar />
    <header class="bg-white shadow">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900">
          Create New Project
        </h1>
      </div>
    </header>
    <main>
      <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
          <div class="bg-white shadow overflow-hidden sm:rounded-lg p-6">
            <form @submit.prevent="createProject">
              <div class="space-y-6">
                <!-- Title -->
                <div>
                  <label for="title" class="block text-sm font-medium text-gray-700">Project Title</label>
                  <div class="mt-1">
                    <input type="text" name="title" id="title" v-model="form.title" required
                           class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                  </div>
                </div>

                <!-- Description -->
                <div>
                  <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                  <div class="mt-1">
                    <textarea id="description" name="description" rows="3" v-model="form.description"
                              class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border"></textarea>
                  </div>
                </div>

                <!-- Client -->
                <div>
                  <label for="client" class="block text-sm font-medium text-gray-700">Client Name</label>
                  <div class="mt-1">
                    <input type="text" name="client" id="client" v-model="form.client" required
                           class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                  </div>
                </div>

                <!-- Dates -->
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                  <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                    <div class="mt-1">
                      <input type="date" name="start_date" id="start_date" v-model="form.start_date" required
                             class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                    </div>
                  </div>

                  <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                    <div class="mt-1">
                      <input type="date" name="end_date" id="end_date" v-model="form.end_date" required
                             class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                    </div>
                  </div>
                </div>

                <!-- Status -->
                <div>
                  <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                  <div class="mt-1">
                    <select id="status" name="status" v-model="form.status"
                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                      <option value="in_progress">In Progress</option>
                      <option value="completed">Completed</option>
                      <option value="on_hold">On Hold</option>
                    </select>
                  </div>
                </div>

                <!-- Error Message -->
                <div v-if="error" class="text-red-500 text-sm">
                  {{ error }}
                </div>

                <!-- Actions -->
                <div class="flex justify-end">
                  <router-link :to="{ name: 'Dashboard' }" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3">
                    Cancel
                  </router-link>
                  <button type="submit" :disabled="loading"
                          class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                    {{ loading ? 'Creating...' : 'Create Project' }}
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Navbar from '../components/Navbar.vue';

const router = useRouter();
const loading = ref(false);
const error = ref(null);

const form = reactive({
  title: '',
  description: '',
  client: '',
  start_date: '',
  end_date: '',
  status: 'in_progress'
});

const createProject = async () => {
  loading.value = true;
  error.value = null;
  
  try {
    await axios.post('/api/projects', form);
    router.push({ name: 'Dashboard' });
  } catch (e) {
    if (e.response && e.response.data && e.response.data.message) {
      error.value = e.response.data.message;
    } else {
      error.value = 'Failed to create project. Please try again.';
    }
  } finally {
    loading.value = false;
  }
};
</script>
