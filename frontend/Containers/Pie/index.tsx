import { PieChart } from '@mantine/charts'
import { Container, Text, Group, Chip, NumberFormatter } from '@mantine/core'
import { useEffect, useState } from 'react'
import PieApi from 'Services/PieApi'
import useUser from 'Hooks/useUser'
import { User, UserContextProps } from 'Contexts'

interface Expense {
    id: number
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
    const { user } = useUser() as UserContextProps & { user: User }

    const [selectedIncomes, setSelectedIncomes] = useState<string[]>([])
    const [expensesData, setExpensesData] = useState<Expense[]>([])
    const [isLoading, setIsLoading] = useState<boolean>(true)
    const [error, setError] = useState<string | null>(null)

    useEffect(() => {
        const fetchData = async () => {
            try {
                const expensesData = (await PieApi.getExpenses()) as Expense[]

                setExpensesData(expensesData)

                setSelectedIncomes(expensesData.map(({ id }) => String(id)))
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
            <Text size="xl" fw={700} ta="center" mb="lg">
                Pie
            </Text>

            <Group gap="xs" wrap="wrap">
                <Chip.Group
                    multiple
                    value={selectedIncomes}
                    onChange={setSelectedIncomes}
                >
                    {expensesData.map(({ id, category, amount }) => (
                        <Chip key={id} value={String(id)}>
                            {category} (
                            <NumberFormatter
                                value={amount}
                                decimalScale={2}
                                suffix={' ' + user.currency.currency}
                            />
                            )
                        </Chip>
                    ))}
                </Chip.Group>
            </Group>

            <PieChart
                size={300}
                withLabelsLine
                labelsPosition="outside"
                labelsType="value"
                withLabels
                withTooltip
                tooltipDataSource="segment"
                data={expensesData
                    .filter(({ id }) => selectedIncomes.includes(String(id)))
                    .map(({ category, amount }, index) => ({
                        name: category,
                        value: amount,
                        color: colors[index],
                    }))}
                style={{ margin: '0 auto' }}
            />
        </Container>
    )
}

export default Pie
