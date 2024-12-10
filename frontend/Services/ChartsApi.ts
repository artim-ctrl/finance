import { Api } from './Api'

class ChartsApi extends Api {
    constructor(baseURL: string) {
        super(baseURL)
    }

    async getExpenses() {
        return await this.get('/v1/charts')
    }
}

export default new ChartsApi(import.meta.env.VITE_BASE_API_URL)
