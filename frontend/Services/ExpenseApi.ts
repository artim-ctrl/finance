import { Api } from './Api'
import { CreateExpenseData } from 'Containers/Home/Expenses/CreateExpense'

class ExpenseApi extends Api {
    constructor(baseURL: string) {
        super(baseURL)
    }

    async getCategories(year: number, month: number) {
        return await this.get(`/v1/expenses/${year}/${month}`)
    }

    async create(data: CreateExpenseData) {
        return await this.post('/v1/expenses', data)
    }

    async updatePlan(data: {
        year: number
        month: number
        categoryId: number
        amount: number
    }) {
        return await this.put('/v1/expenses/plans', data)
    }
}

export default new ExpenseApi(import.meta.env.VITE_BASE_API_URL)
