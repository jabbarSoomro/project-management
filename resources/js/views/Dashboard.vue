<template>
  <div>
    <Navbar />
    <header class="bg-white shadow">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">
          Dashboard
        </h1>
        <router-link :to="{ name: 'ProjectCreate' }" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">
          Create Project
        </router-link>
      </div>
    </header>
    <main>
      <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
          <div v-if="loading" class="text-center">
            <p class="text-gray-500">Loading projects...</p>
          </div>
          <div v-else-if="error" class="text-center text-red-500">
            {{ error }}
          </div>
          <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div v-for="project in projects" :key="project.id" class="bg-white overflow-hidden shadow rounded-lg hover:shadow-md transition-shadow duration-200">
              <router-link :to="{ name: 'ProjectDetails', params: { id: project.id } }" class="block p-6">
                <div class="flex items-center justify-between">
                  <h3 class="text-lg font-medium text-gray-900">{{ project.title }}</h3>
                  <span :class="statusClass(project.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                    {{ project.status }}
                  </span>
                </div>
                <p class="mt-2 text-sm text-gray-500">{{ project.client }}</p>
                <div class="mt-4">
                  <div class="flex justify-between text-sm text-gray-500">
                    <span>Start: {{ formatDate(project.start_date) }}</span>
                    <span>End: {{ formatDate(project.end_date) }}</span>
                  </div>
                </div>
              </router-link>
            </div>
          </div>
          
          <!-- Pagination (Simple) -->
          <div v-if="meta.last_page > 1" class="mt-6 flex justify-center">
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
              <button @click="fetchProjects(meta.current_page - 1)" :disabled="meta.current_page === 1" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                Previous
              </button>
              <button @click="fetchProjects(meta.current_page + 1)" :disabled="meta.current_page === meta.last_page" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                Next
              </button>
            </nav>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Navbar from '../components/Navbar.vue';

const projects = ref([]);
const meta = ref({});
const loading = ref(true);
const error = ref(null);

const fetchProjects = async (page = 1) => {
  loading.value = true;
  try {
    const response = await axios.get(`/api/projects?page=${page}`);
    projects.value = response.data.data;
    meta.value = response.data.meta;
  } catch (e) {
    error.value = 'Failed to load projects';
  } finally {
    loading.value = false;
  }
};

const statusClass = (status) => {
  switch (status) {
    case 'completed': return 'bg-green-100 text-green-800';
    case 'in_progress': return 'bg-blue-100 text-blue-800';
    case 'on_hold': return 'bg-yellow-100 text-yellow-800';
    default: return 'bg-gray-100 text-gray-800';
  }
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString();
};

onMounted(() => {
  fetchProjects();
});
</script>
