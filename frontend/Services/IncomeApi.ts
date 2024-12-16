import { Api } from './Api'
import { CreateIncomeData } from 'Containers/Home/Incomes/CreateIncome'

class IncomeApi extends Api {
    constructor(baseURL: string) {
        super(baseURL)
    }

    async getCategories(year: number, month: number) {
        return await this.get(`/v1/incomes/${year}/${month}`)
    }

    async create(year: number, month: number, data: CreateIncomeData) {
        return await this.post(`/v1/incomes/${year}/${month}`, data)
    }

    async update(
        year: number,
        month: number,
        data: { category_id: number; amount: number },
    ) {
        return await this.put(`/v1/incomes/${year}/${month}`, data)
    }
}

export default new IncomeApi(import.meta.env.VITE_BASE_API_URL)
