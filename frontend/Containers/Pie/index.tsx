import { PieChart } from '@mantine/charts'
import { Title, Container } from '@mantine/core'
import { useEffect, useState } from 'react'
import PieApi from 'Services/PieApi'

interface Expense {
    category: string
    amount: number
}

const colors = [
    'cyan.6',
    'indigo.6',
    'yellow.6',
    'blue.6',
    'red.6',
    'green.6',
    'violet.6',
    'pink.6',
    'grape.6',
    'teal.6',
    'lime.6',
    'orange.6',
]

const Pie = () => {
    const [expensesData, setExpensesData] = useState<Expense[]>([])
    const [isLoading, setIsLoading] = useState<boolean>(true)
    const [error, setError] = useState<string | null>(null)

    useEffect(() => {
        const fetchData = async () => {
            try {
                setExpensesData((await PieApi.getExpenses()) as Expense[])
            } catch {
                setError('Error fetching expenses data')
            } finally {
                setIsLoading(false)
            }
        }

        fetchData()
    }, [])

    if (isLoading) {
        return <div>Loading...</div>
    }

    if (error) {
        return <div>{error}</div>
    }

    return (
        <Container size="xl" mt="md">
            <Title order={2}>Pie</Title>

            <PieChart
                size={300}
                withLabelsLine
                labelsPosition="outside"
                labelsType="value"
                withLabels
                withTooltip
                tooltipDataSource="segment"
                data={expensesData.map(({ category, amount }, index) => ({
                    name: category,
                    value: amount,
                    color: colors[index],
                }))}
            />
        </Container>
    )
}

export default Pie
