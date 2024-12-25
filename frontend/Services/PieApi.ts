import { Api } from './Api'

class PieApi extends Api {
    constructor(baseURL: string) {
        super(baseURL)
    }

    async getExpenses(from: string, to: string) {
        return await this.get('/v1/pie', { from, to })
    }
}

export default new PieApi(import.meta.env.VITE_BASE_API_URL)
