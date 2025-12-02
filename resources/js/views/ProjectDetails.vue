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
              <button class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">
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
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import Navbar from '../components/Navbar.vue';

const route = useRoute();
const project = ref(null);
const loading = ref(true);
const error = ref(null);

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
