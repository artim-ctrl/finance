import { Api } from './Api'
import { User } from 'Contexts'

class AuthApi extends Api {
    constructor(baseURL: string) {
        super(baseURL)
    }

    async register(data: { name: string; email: string; password: string }) {
        return await this.post<User>('/v1/auth/register', data)
    }

    async login(data: { email: string; password: string }) {
        return await this.post<User>('/v1/auth/login', data)
    }

    async profile() {
        return await this.get<User | null>('/v1/auth/profile')
    }

    async logout() {
        return await this.post('/v1/auth/logout')
    }
}

export default new AuthApi(import.meta.env.VITE_BASE_API_URL)
