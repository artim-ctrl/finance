import { Api } from './Api'
import { CreateIncomeData } from 'Containers/Home/Incomes/CreateIncome'

class IncomeApi extends Api {
    constructor(baseURL: string) {
        super(baseURL)
    }

    async getCategories(year: number, month: number) {
        return await this.get(`/v1/incomes/${year}/${month}`)
    }

    async create(data: CreateIncomeData) {
        return await this.post('/v1/incomes', data)
    }
}

export default new IncomeApi(import.meta.env.VITE_BASE_API_URL)
