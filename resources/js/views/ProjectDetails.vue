<template>
  <div>
    <Navbar />
    <header class="bg-white shadow">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">
          {{ project?.title || 'Loading...' }}
        </h1>
        <router-link :to="{ name: 'Dashboard' }" class="text-indigo-600 hover:text-indigo-900">
          &larr; Back to Dashboard
        </router-link>
      </div>
    </header>
    <main>
      <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div v-if="loading" class="text-center">
          <p class="text-gray-500">Loading project details...</p>
        </div>
        <div v-else-if="error" class="text-center text-red-500">
          {{ error }}
        </div>
        <div v-else class="px-4 py-6 sm:px-0">
          <!-- Project Info -->
          <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6">
              <h3 class="text-lg leading-6 font-medium text-gray-900">Project Information</h3>
              <p class="mt-1 max-w-2xl text-sm text-gray-500">Details and client info.</p>
            </div>
            <div class="border-t border-gray-200">
              <dl>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">Client</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ project.client }}</dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">Status</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    <span :class="statusClass(project.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                      {{ project.status }}
                    </span>
                  </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">Timeline</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    {{ formatDate(project.start_date) }} - {{ formatDate(project.end_date) }}
                  </dd>
                </div>
              </dl>
            </div>
          </div>

          <!-- Tasks -->
          <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
              <h3 class="text-lg leading-6 font-medium text-gray-900">Tasks</h3>
              <!-- Add Task Button (Placeholder) -->
              <button @click="openTaskModal" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">
                Add Task
              </button>
            </div>
            <ul role="list" class="divide-y divide-gray-200">
              <li v-for="task in project.tasks" :key="task.id" class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                <div class="flex items-center justify-between">
                  <p class="text-sm font-medium text-indigo-600 truncate">{{ task.title }}</p>
                  <div class="ml-2 flex-shrink-0 flex">
                    <span :class="statusClass(task.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                      {{ task.status }}
                    </span>
                  </div>
                </div>
                <div class="mt-2 sm:flex sm:justify-between">
                  <div class="sm:flex">
                    <p class="flex items-center text-sm text-gray-500">
                      Deadline: {{ formatDate(task.deadline) }}
                    </p>
                  </div>
                </div>
              </li>
              <li v-if="project.tasks.length === 0" class="px-4 py-4 sm:px-6 text-center text-gray-500">
                No tasks found.
              </li>
            </ul>
          </div>
        </div>
      </div>
    </main>

    <!-- Task Creation Modal -->
    <!-- Task Creation Modal -->
    <div v-if="showTaskModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

      <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
          <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                  <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                    Add New Task
                  </h3>
                  <div class="mt-2">
                    <form @submit.prevent="createTask">
                      <div class="space-y-4">
                        <div>
                          <label for="task-title" class="block text-sm font-medium text-gray-700">Title</label>
                          <input type="text" id="task-title" v-model="taskForm.title" required
                                 class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md border p-2">
                        </div>
                        <div>
                          <label for="task-desc" class="block text-sm font-medium text-gray-700">Description</label>
                          <textarea id="task-desc" v-model="taskForm.description" rows="3"
                                    class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md border p-2"></textarea>
                        </div>
                        <div>
                          <label for="task-deadline" class="block text-sm font-medium text-gray-700">Deadline</label>
                          <input type="date" id="task-deadline" v-model="taskForm.deadline" required
                                 class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md border p-2">
                        </div>
                        <div>
                          <label for="task-status" class="block text-sm font-medium text-gray-700">Status</label>
                          <select id="task-status" v-model="taskForm.status"
                                  class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md border p-2">
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="blocked">Blocked</option>
                            <option value="on_hold">On Hold</option>
                          </select>
                        </div>
                      </div>
                      <div v-if="taskError" class="mt-2 text-red-500 text-sm">
                        {{ taskError }}
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <button type="button" @click="createTask" :disabled="taskLoading"
                      class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                {{ taskLoading ? 'Saving...' : 'Save Task' }}
              </button>
              <button type="button" @click="closeTaskModal"
                      class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                Cancel
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import Navbar from '../components/Navbar.vue';

const route = useRoute();
const project = ref(null);
const loading = ref(true);
const error = ref(null);
const showTaskModal = ref(false);
const taskLoading = ref(false);
const taskError = ref(null);

const taskForm = reactive({
  title: '',
  description: '',
  deadline: '',
  status: 'in_progress'
});

const fetchProject = async () => {
  loading.value = true;
  try {
    const response = await axios.get(`/api/projects/${route.params.id}`);
    project.value = response.data.data;
  } catch (e) {
    error.value = 'Failed to load project details';
  } finally {
    loading.value = false;
  }
};

const createTask = async () => {
  taskLoading.value = true;
  taskError.value = null;
  try {
    await axios.post(`/api/projects/${route.params.id}/tasks`, taskForm);
    // Refresh project details to show new task
    await fetchProject();
    closeTaskModal();
    // Reset form
    taskForm.title = '';
    taskForm.description = '';
    taskForm.deadline = '';
    taskForm.status = 'in_progress';
  } catch (e) {
    if (e.response && e.response.data && e.response.data.message) {
      taskError.value = e.response.data.message;
    } else {
      taskError.value = 'Failed to create task';
    }
  } finally {
    taskLoading.value = false;
  }
};

const openTaskModal = () => {
  showTaskModal.value = true;
};

const closeTaskModal = () => {
  showTaskModal.value = false;
  taskError.value = null;
};

const statusClass = (status) => {
  switch (status) {
    case 'completed': return 'bg-green-100 text-green-800';
    case 'in_progress': return 'bg-blue-100 text-blue-800';
    case 'blocked': return 'bg-red-100 text-red-800';
    case 'on_hold': return 'bg-yellow-100 text-yellow-800';
    default: return 'bg-gray-100 text-gray-800';
  }
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString();
};

onMounted(() => {
  fetchProject();
});
</script>
