import { useEffect, useState } from 'react'
import {
    Box,
    Chip,
    Text,
    Container,
    Group,
    Paper,
    NumberFormatter,
} from '@mantine/core'
import IncomeApi from 'Services/IncomeApi'
import dayjs from 'dayjs'
import ExpenseApi from 'Services/ExpenseApi'
import useUser from 'Hooks/useUser'
import { User, UserContextProps } from 'Contexts'

const SELECTED_KEY = 'selected-categories'

interface IncomeCategory {
    id: number
    name: string
    incomes?: { amount: number }[]
}

interface ExpenseCategory {
    id: number
    name: string
    expenses?: { date: string; amount: number }[]
}

const Summary = () => {
    const { user } = useUser() as UserContextProps & { user: User }

    const [isLoading, setIsLoading] = useState(true)
    const [incomes, setIncomes] = useState<
        { id: number; name: string; amount: number }[]
    >([])
    const [selectedIncomes, setSelectedIncomes] = useState<string[]>([])
    const [expenses, setExpenses] = useState<
        { id: number; name: string; amount: number }[]
    >([])
    const [selectedExpenses, setSelectedExpenses] = useState<string[]>([])

    useEffect(() => {
        if (!isLoading) {
            localStorage.setItem(
                SELECTED_KEY,
                JSON.stringify({
                    incomes: selectedIncomes,
                    expenses: selectedExpenses,
                }),
            )
        }
    }, [selectedIncomes, selectedExpenses])

    const load = async () => {
        try {
            const incomeCategories = (await IncomeApi.getCategories(
                dayjs().year(),
                dayjs().month(),
            )) as IncomeCategory[]

            setIncomes(
                incomeCategories.map(({ id, name, incomes }) => ({
                    id,
                    name,
                    amount:
                        incomes !== undefined
                            ? incomes.reduce(
                                  (acc, { amount }) => acc + amount,
                                  0,
                              )
                            : 0,
                })),
            )

            const expenseCategories = (await ExpenseApi.getCategories(
                dayjs().year(),
                dayjs().month(),
            )) as ExpenseCategory[]

            setExpenses(
                expenseCategories.map(({ id, name, expenses }) => ({
                    id,
                    name,
                    amount:
                        expenses !== undefined
                            ? expenses.reduce(
                                  (acc, { amount }) => acc + amount,
                                  0,
                              )
                            : 0,
                })),
            )

            setSelectedIncomes(
                JSON.parse(localStorage.getItem(SELECTED_KEY) || '{}')
                    .incomes || expenseCategories.map(({ id }) => String(id)),
            )
            setSelectedExpenses(
                JSON.parse(localStorage.getItem(SELECTED_KEY) || '{}')
                    .expenses || expenseCategories.map(({ id }) => String(id)),
            )
        } finally {
            setIsLoading(false)
        }
    }

    useEffect(() => {
        load()
    }, [])

    const totalIncome = incomes
        .filter(({ id }) => selectedIncomes.includes(String(id)))
        .reduce((sum, item) => sum + item.amount, 0)

    const totalExpense = expenses
        .filter(({ id }) => selectedExpenses.includes(String(id)))
        .reduce((sum, item) => sum + item.amount, 0)

    const netBalance = totalIncome - totalExpense

    return (
        <Container size="xl" mt="md">
            <Text size="xl" fw={700} ta="center" mb="lg">
                Summary
            </Text>

            <Text fw={500} mb="sm">
                Income Categories:
            </Text>
            <Group gap="xs" wrap="wrap">
                <Chip.Group
                    multiple
                    value={selectedIncomes}
                    onChange={setSelectedIncomes}
                >
                    {incomes.map(({ id, name, amount }) => (
                        <Chip key={id} value={String(id)}>
                            {name} (
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

            <Text fw={500} mt="lg" mb="sm">
                Expense Categories:
            </Text>
            <Group gap="xs" wrap="wrap">
                <Chip.Group
                    multiple
                    value={selectedExpenses}
                    onChange={setSelectedExpenses}
                >
                    {expenses.map(({ id, name, amount }) => (
                        <Chip key={id} value={String(id)}>
                            {name} (
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

            <Paper mt="xl" shadow="md" p="lg" withBorder>
                <Box>
                    <Text size="lg" fw={700} ta="center">
                        Financial Summary
                    </Text>
                </Box>
                <Box
                    mt="md"
                    ta="center"
                    style={{
                        display: 'flex',
                        justifyContent: 'center',
                        alignItems: 'center',
                    }}
                >
                    <Text
                        size="lg"
                        fw={500}
                        c="green"
                        style={{ marginRight: '10px' }}
                    >
                        <NumberFormatter
                            value={totalIncome}
                            decimalScale={2}
                            suffix={' ' + user.currency.currency}
                        />
                    </Text>
                    <Text size="lg" fw={700} style={{ margin: '0 10px' }}>
                        -
                    </Text>
                    <Text
                        size="lg"
                        fw={500}
                        c="red"
                        style={{ marginLeft: '10px' }}
                    >
                        <NumberFormatter
                            value={totalExpense}
                            decimalScale={2}
                            suffix={' ' + user.currency.currency}
                        />
                    </Text>
                    <Text size="lg" fw={700} style={{ margin: '0 10px' }}>
                        =
                    </Text>
                    <Text
                        size="lg"
                        fw={500}
                        c={netBalance >= 0 ? 'blue' : 'orange'}
                        style={{ marginLeft: '10px' }}
                    >
                        <NumberFormatter
                            value={netBalance}
                            decimalScale={2}
                            suffix={' ' + user.currency.currency}
                        />
                    </Text>
                </Box>
            </Paper>
        </Container>
    )
}

export default Summary
