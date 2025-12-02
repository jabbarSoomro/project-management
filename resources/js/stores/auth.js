import { defineStore } from 'pinia';
import axios from 'axios';
import router from '../router';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        token: localStorage.getItem('token') || null,
        user: JSON.parse(localStorage.getItem('user')) || null,
    }),
    actions: {
        async login(email, password) {
            try {
                const response = await axios.post('/api/login', { email, password });
                this.token = response.data.token;
                this.user = response.data.user;

                localStorage.setItem('token', this.token);
                localStorage.setItem('user', JSON.stringify(this.user));

                axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;

                router.push({ name: 'Dashboard' });
            } catch (error) {
                throw error;
            }
        },
        async logout() {
            try {
                await axios.post('/api/logout');
            } catch (error) {
                console.error('Logout failed', error);
            } finally {
                this.token = null;
                this.user = null;
                localStorage.removeItem('token');
                localStorage.removeItem('user');
                delete axios.defaults.headers.common['Authorization'];
                router.push({ name: 'Login' });
            }
        }
    }
});
