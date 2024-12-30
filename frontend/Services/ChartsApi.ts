import { Api } from './Api'

class ChartsApi extends Api {
    constructor(baseURL: string) {
        super(baseURL)
    }

    async getExpenses(categories: string[] | null) {
        return await this.get('/v1/charts', {
            categories: categories ?? undefined,
        })
    }
}

export default new ChartsApi(import.meta.env.VITE_BASE_API_URL)
