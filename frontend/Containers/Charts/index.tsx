import { useEffect, useState } from 'react'
import { LineChart } from '@mantine/charts'
import ChartsApi from 'Services/ChartsApi'
import { Chip, Container, Group, Title } from '@mantine/core'

interface Category {
    id: number
    name: string
}

interface Expense {
    date: string
    currentAmount: string
    previousAmount: string
}

const Charts = () => {
    const [expensesData, setExpensesData] = useState<Expense[]>([])
    const [categories, setCategories] = useState<Category[]>([])
    const [selectedCategories, setSelectedCategories] = useState<string[]>([])
    const [isLoading, setIsLoading] = useState<boolean>(true)
    const [error, setError] = useState<string | null>(null)

    const fetchData = async (categories: string[] | null = null) => {
        try {
            const response = (await ChartsApi.getExpenses(categories)) as {
                expenses: Expense[]
                categories: Category[]
            }

            setExpensesData(response.expenses as Expense[])
            setCategories(response.categories as Category[])
        } catch {
            setError('Error fetching expenses data')
        } finally {
            setIsLoading(false)
        }
    }

    useEffect(() => {
        fetchData().then(() => {
            setSelectedCategories(categories.map(({ id }) => String(id)))
        })
    }, []) // eslint-disable-line react-hooks/exhaustive-deps

    const onSelectCategory = (value: string[]) => {
        setSelectedCategories(value)

        fetchData(value)
    }

    if (isLoading) {
        return <div>Loading...</div>
    }

    if (error !== null) {
        return <div>{error}</div>
    }

    return (
        <Container size="xl" mt="md">
            <Title order={2}>Expense Trends</Title>

            <Chip.Group
                multiple
                value={selectedCategories}
                onChange={onSelectCategory}
            >
                <Group justify="center">
                    {categories.map(({ id, name }, i) => (
                        <Chip key={i} variant="outline" value={String(id)}>
                            {name}
                        </Chip>
                    ))}
                </Group>
            </Chip.Group>

            <LineChart
                h={400}
                data={expensesData}
                dataKey="date"
                withLegend
                series={[
                    {
                        label: 'December 2024',
                        name: 'currentAmount',
                        color: 'indigo.6',
                    },
                    {
                        label: 'November 2024',
                        name: 'previousAmount',
                        color: 'teal.6',
                    },
                ]}
            />
        </Container>
    )
}

export default Charts
