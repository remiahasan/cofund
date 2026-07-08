import { authService } from "@/services/authService";
import { defineStore } from "pinia";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: null,
        token: null,
    }),
    getters: {
        isAuthenticated: (state) => !!state.token,
    },
    actions: {
        setUser(user) {
            this.user = user;
            localStorage.setItem("user", JSON.stringify(user));
        },
        setToken(token) {
            this.token = token;
            localStorage.setItem("token", token);
        },
        clearAuth() {
            this.user = null;
            this.token = null;
            localStorage.removeItem("user");
            localStorage.removeItem("token");
        },
        async login(email, password) {
            try {
                const response = await authService.login(email, password);
                this.setUser(response.data.user);
                this.setToken(response.data.token);
                return response;
            } catch (error) {
                throw error;
            }
        },
        async register(nama, email, password, confirm_password){
            try {
                const response = await authService.register(nama, email, password, confirm_password);
                this.setUser(response.data.user);
                this.setToken(response.data.token);
                return response;
            } catch (error) {
                throw error;
            }
        }
    },
});