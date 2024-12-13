import { useEffect, useState } from 'react'
import { LineChart } from '@mantine/charts'
import ChartsApi from 'Services/ChartsApi'
import { Container, Title } from '@mantine/core'

interface Expense {
    date: string
    last30Days: string
    previous30Days: string
}

const Charts = () => {
    const [expensesData, setExpensesData] = useState<Expense[]>([])
    const [isLoading, setIsLoading] = useState<boolean>(true)
    const [error, setError] = useState<string | null>(null)

    useEffect(() => {
        const fetchData = async () => {
            try {
                setExpensesData((await ChartsApi.getExpenses()) as Expense[])
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
            <Title order={2}>Expense Trends</Title>
            <LineChart
                h={400}
                data={expensesData}
                dataKey="date"
                withLegend
                series={[
                    {
                        label: 'Last 30 days',
                        name: 'last30Days',
                        color: 'indigo.6',
                    },
                    {
                        label: 'Previous 30 days',
                        name: 'previous30Days',
                        color: 'teal.6',
                    },
                ]}
            />
        </Container>
    )
}

export default Charts
