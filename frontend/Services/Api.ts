import axios, { AxiosInstance, AxiosResponse } from 'axios'
import ROUTES from 'Constants/routes'

export class Api {
    protected client: AxiosInstance

    constructor(baseURL: string) {
        this.client = axios.create({
            baseURL,
            withCredentials: true,
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
        })

        this.setupInterceptors()
    }

    private setupInterceptors() {
        this.client.interceptors.response.use(
            (response: AxiosResponse) => response,
            async (error) => {
                if (
                    error.response?.status === 401 &&
                    error.config._retry !== true
                ) {
                    error.config._retry = true

                    try {
                        await this.client.request({
                            ...error.config,
                            url: '/v1/auth/refresh',
                        })

                        return this.client.request(error.config)
                    } catch {
                        if (error.config.url !== '/v1/auth/refresh') {
                            window.location.href = ROUTES.LOGIN
                        }
                    }
                }

                return Promise.reject(error)
            },
        )
    }

    protected get<T>(url: string, params?: object): Promise<T> {
        return this.client
            .get<T>(url, { params })
            .then((response) => response.data)
    }

    protected post<T>(url: string, data?: object): Promise<T> {
        return this.client.post<T>(url, data).then((response) => response.data)
    }

    protected put<T>(url: string, data?: object): Promise<T> {
        return this.client.put<T>(url, data).then((response) => response.data)
    }

    protected delete<T>(url: string): Promise<T> {
        return this.client.delete<T>(url).then((response) => response.data)
    }
}
