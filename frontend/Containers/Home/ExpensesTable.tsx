import { useEffect, useState } from 'react'
import {
    Table,
    Title,
    NumberInput,
    Button,
    Notification,
    Flex,
    Box,
    LoadingOverlay,
} from '@mantine/core'
import dayjs from 'dayjs'
import ExpenseApi from 'Services/ExpenseApi'
import CreateExpenseModal from './CreateExpenseModal'

interface ExpenseCategory {
    id: number
    name: string
    monthly_expense_plans?: { amount: number }[]
    expenses?: { date: string; amount: number }[]
}

interface ExpensesTableProps {
    currentMonth: Date
}

const ExpensesTable = ({ currentMonth }: ExpensesTableProps) => {
    const [isLoading, setIsLoading] = useState(true)
    const [categories, setCategories] = useState<ExpenseCategory[]>([])
    const [error, setError] = useState<string | null>(null)
    const [modalOpen, setModalOpen] = useState(false)

    const fetchCategories = async (currentMonth: Date) => {
        setIsLoading(true)
        setError(null)

        try {
            const data = (await ExpenseApi.getCategories(
                currentMonth.getFullYear(),
                currentMonth.getMonth() + 1,
            )) as ExpenseCategory[]

            setCategories(data)
        } catch (e) {
            setError((e as Error).message || 'Failed to fetch categories')
        } finally {
            setIsLoading(false)
        }
    }

    useEffect(() => {
        fetchCategories(currentMonth)
    }, [currentMonth])

    const daysInMonth = Array.from(
        { length: dayjs(currentMonth).daysInMonth() },
        (_, i) => i + 1,
    )

    const calculateActualExpenses = (category: ExpenseCategory): number => {
        if (!category.expenses) return 0

        return category.expenses.reduce(
            (sum, expense) => sum + expense.amount,
            0,
        )
    }

    const calculateExpenseByDay = (category: ExpenseCategory, day: number) => {
        if (!category.expenses) return 0

        const dateStr = dayjs(currentMonth).date(day).format('YYYY-MM-DD')

        return category.expenses.reduce((acc, expense) => {
            if (dayjs(expense.date).format('YYYY-MM-DD') !== dateStr) {
                return acc
            }

            return acc + expense.amount
        }, 0)
    }

    const calculateDailyTotal = (day: number): number => {
        return categories.reduce(
            (sum, category) => sum + calculateExpenseByDay(category, day),
            0,
        )
    }

    const totalPlannedExpenses = categories.reduce(
        (sum, cat) => sum + (cat.monthly_expense_plans?.[0]?.amount || 0),
        0,
    )
    const totalActualExpenses = categories.reduce(
        (sum, cat) => sum + calculateActualExpenses(cat),
        0,
    )
    const totalDeviation = totalActualExpenses - totalPlannedExpenses

    const handleMonthlyExpenseChange = async (
        categoryId: number,
        value: number,
    ) => {
        try {
            await ExpenseApi.updatePlan({
                year: currentMonth.getFullYear(),
                month: currentMonth.getMonth() + 1,
                categoryId: categoryId,
                amount: value,
            })

            await fetchCategories(currentMonth)
        } catch (e) {
            setError((e as Error).message || 'Failed to update expense plan')
        }
    }

    const handleDailyExpenseChange = async (
        categoryId: number,
        day: number,
        value: number,
    ) => {
        try {
            await ExpenseApi.create({
                date: dayjs(currentMonth).date(day).format('YYYY-MM-DD'),
                categoryId: categoryId,
                amount: value,
            })

            await fetchCategories(currentMonth)
        } catch (e) {
            setError((e as Error).message || 'Failed to create expense')
        }
    }

    return (
        <div style={{ position: 'relative', marginTop: '1rem' }}>
            {error !== null && (
                <Notification
                    color="red"
                    onClose={() => setError(null)}
                    style={{ marginBottom: '1rem' }}
                >
                    {error}
                </Notification>
            )}

            <Flex justify="space-between" align="center" mt="md">
                <Title order={2}>Expenses</Title>
                <Button onClick={() => setModalOpen(true)}>Add Expense</Button>
            </Flex>

            <CreateExpenseModal
                isOpen={modalOpen}
                onClose={() => setModalOpen(false)}
                onExpenseCreated={() => fetchCategories(currentMonth)}
            />

            <Box>
                <LoadingOverlay
                    visible={isLoading}
                    zIndex={1000}
                    overlayProps={{ radius: 'sm', blur: 2 }}
                />

                <Table.ScrollContainer minWidth={1000}>
                    <Table striped mt="lg">
                        <Table.Thead>
                            <Table.Tr>
                                <Table.Th>Category</Table.Th>
                                <Table.Th>Planned expenses (RSD)</Table.Th>
                                <Table.Th>Actual expenses (RSD)</Table.Th>
                                <Table.Th>Deviation from plan (RSD)</Table.Th>
                                {daysInMonth.map((day) => (
                                    <Table.Th key={day}>Day {day}</Table.Th>
                                ))}
                            </Table.Tr>
                        </Table.Thead>
                        <Table.Tbody>
                            {categories.map((category) => (
                                <Table.Tr key={category.id}>
                                    <Table.Td>{category.name}</Table.Td>
                                    <Table.Td>
                                        <NumberInput
                                            value={
                                                category
                                                    .monthly_expense_plans?.[0]
                                                    ?.amount || ''
                                            }
                                            onBlur={(event) =>
                                                handleMonthlyExpenseChange(
                                                    category.id,
                                                    parseFloat(
                                                        event.target.value,
                                                    ) || 0,
                                                )
                                            }
                                            style={{ width: '100px' }}
                                            placeholder="0.00"
                                            min={0}
                                            decimalScale={2}
                                            step={0.01}
                                        />
                                    </Table.Td>
                                    <Table.Td>
                                        {calculateActualExpenses(
                                            category,
                                        ).toLocaleString()}
                                    </Table.Td>
                                    <Table.Td>
                                        <span
                                            style={{
                                                color:
                                                    calculateActualExpenses(
                                                        category,
                                                    ) >
                                                    (category
                                                        .monthly_expense_plans?.[0]
                                                        ?.amount || 0)
                                                        ? 'red'
                                                        : 'green',
                                                fontWeight: 'bold',
                                            }}
                                        >
                                            {(
                                                calculateActualExpenses(
                                                    category,
                                                ) -
                                                (category
                                                    .monthly_expense_plans?.[0]
                                                    ?.amount || 0)
                                            ).toFixed(2)}
                                        </span>
                                    </Table.Td>
                                    {daysInMonth.map((day) => {
                                        const dayExpense =
                                            calculateExpenseByDay(category, day)
                                        return (
                                            <Table.Td key={day}>
                                                <NumberInput
                                                    value={
                                                        dayExpense === 0
                                                            ? ''
                                                            : dayExpense
                                                    }
                                                    onBlur={(event) => {
                                                        const value =
                                                            parseFloat(
                                                                event.target
                                                                    .value,
                                                            ) || 0
                                                        if (
                                                            value !== dayExpense
                                                        ) {
                                                            handleDailyExpenseChange(
                                                                category.id,
                                                                day,
                                                                value -
                                                                    calculateExpenseByDay(
                                                                        category,
                                                                        day,
                                                                    ),
                                                            )
                                                        }
                                                    }}
                                                    style={{ width: '100px' }}
                                                    placeholder="0.00"
                                                    min={0}
                                                    decimalScale={2}
                                                    step={0.01}
                                                />
                                            </Table.Td>
                                        )
                                    })}
                                </Table.Tr>
                            ))}
                            <Table.Tr>
                                <Table.Td>
                                    <strong>Totals</strong>
                                </Table.Td>
                                <Table.Td>
                                    <strong>
                                        {totalPlannedExpenses.toLocaleString()}
                                    </strong>
                                </Table.Td>
                                <Table.Td>
                                    <strong>
                                        {totalActualExpenses.toLocaleString()}
                                    </strong>
                                </Table.Td>
                                <Table.Td>
                                    <strong
                                        style={{
                                            color:
                                                totalDeviation > 0
                                                    ? 'red'
                                                    : 'green',
                                        }}
                                    >
                                        {totalDeviation.toFixed(2)}
                                    </strong>
                                </Table.Td>
                                {daysInMonth.map((day) => (
                                    <Table.Td key={day}>
                                        <strong>
                                            {calculateDailyTotal(day).toFixed(
                                                2,
                                            )}
                                        </strong>
                                    </Table.Td>
                                ))}
                            </Table.Tr>
                        </Table.Tbody>
                    </Table>
                </Table.ScrollContainer>
            </Box>
        </div>
    )
}

export default ExpensesTable
