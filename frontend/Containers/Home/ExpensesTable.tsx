import { useEffect, useState } from 'react'
import {
    Table,
    Title,
    NumberInput,
    Button,
    Flex,
    Box,
    LoadingOverlay,
} from '@mantine/core'
import dayjs from 'dayjs'
import ExpenseApi from 'Services/ExpenseApi'
import CreateExpenseModal from './CreateExpenseModal'
import useUser from 'Hooks/useUser'
import { User, UserContextProps } from 'Contexts'
import { showError } from 'Services/notify'

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
    const [modalOpen, setModalOpen] = useState(false)
    const { user } = useUser() as UserContextProps & { user: User }

    const fetchCategories = async (currentMonth: Date) => {
        setIsLoading(true)

        try {
            const data = (await ExpenseApi.getCategories(
                currentMonth.getFullYear(),
                currentMonth.getMonth() + 1,
            )) as ExpenseCategory[]

            setCategories(data)
        } catch (e) {
            showError((e as Error).message || 'Failed to fetch categories')
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
            showError((e as Error).message || 'Failed to update expense plan')
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
            showError((e as Error).message || 'Failed to create expense')
        }
    }

    return (
        <div style={{ position: 'relative', marginTop: '1rem' }}>
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
                    <Table striped withColumnBorders mt="lg" ta="center">
                        <Table.Thead>
                            <Table.Tr>
                                <Table.Th w="190">Category</Table.Th>
                                <Table.Th>
                                    Planned expenses ({user.currency.currency})
                                </Table.Th>
                                <Table.Th>
                                    Actual expenses ({user.currency.currency})
                                </Table.Th>
                                <Table.Th>
                                    Deviation from plan (
                                    {user.currency.currency})
                                </Table.Th>
                                {daysInMonth.map((day) => (
                                    <Table.Th key={day}>Day {day}</Table.Th>
                                ))}
                            </Table.Tr>
                        </Table.Thead>
                        <Table.Tbody>
                            {categories.map((category) => {
                                const currentAmount =
                                    category.monthly_expense_plans?.[0]
                                        ?.amount ?? 0

                                const save = (newAmount: number) => {
                                    if (currentAmount != newAmount) {
                                        handleMonthlyExpenseChange(
                                            category.id,
                                            newAmount,
                                        )
                                    }
                                }

                                return (
                                    <Table.Tr h="40" key={category.id}>
                                        <Table.Td p="0">
                                            {category.name}
                                        </Table.Td>
                                        <Table.Td p="0">
                                            <NumberInput
                                                value={String(
                                                    currentAmount || '',
                                                )}
                                                onBlur={(e) =>
                                                    save(
                                                        parseFloat(
                                                            e.target.value,
                                                        ) || 0,
                                                    )
                                                }
                                                onKeyDown={(e) => {
                                                    if (e.key === 'Enter') {
                                                        save(
                                                            parseFloat(
                                                                e.currentTarget
                                                                    .value,
                                                            ) || 0,
                                                        )
                                                    }
                                                }}
                                                style={{ width: '100px' }}
                                                styles={{
                                                    input: {
                                                        textAlign: 'center',
                                                    },
                                                }}
                                                placeholder="0.00"
                                                min={0}
                                                decimalScale={2}
                                                step={0.01}
                                                hideControls
                                                variant="unstyled"
                                            />
                                        </Table.Td>
                                        <Table.Td p="0">
                                            {calculateActualExpenses(
                                                category,
                                            ).toLocaleString()}
                                        </Table.Td>
                                        <Table.Td p="0">
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
                                                calculateExpenseByDay(
                                                    category,
                                                    day,
                                                )

                                            const saveDay = (value: number) => {
                                                if (value !== dayExpense) {
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
                                            }

                                            return (
                                                <Table.Td
                                                    key={day}
                                                    p="0"
                                                    px="1"
                                                >
                                                    <NumberInput
                                                        value={
                                                            dayExpense === 0
                                                                ? ''
                                                                : dayExpense
                                                        }
                                                        onBlur={(e) => {
                                                            const value =
                                                                parseFloat(
                                                                    e.target
                                                                        .value,
                                                                ) || 0

                                                            saveDay(value)
                                                        }}
                                                        onKeyDown={(e) => {
                                                            if (
                                                                e.key !==
                                                                'Enter'
                                                            ) {
                                                                return
                                                            }

                                                            const value =
                                                                parseFloat(
                                                                    e
                                                                        .currentTarget
                                                                        .value,
                                                                ) || 0

                                                            saveDay(value)
                                                        }}
                                                        style={{
                                                            minWidth: '60px',
                                                        }}
                                                        placeholder="0.00"
                                                        min={0}
                                                        decimalScale={2}
                                                        step={0.01}
                                                        hideControls
                                                        variant="unstyled"
                                                        styles={{
                                                            input: {
                                                                textAlign:
                                                                    'center',
                                                            },
                                                        }}
                                                    />
                                                </Table.Td>
                                            )
                                        })}
                                    </Table.Tr>
                                )
                            })}
                            <Table.Tr>
                                <Table.Td px="0">
                                    <strong>Totals</strong>
                                </Table.Td>
                                <Table.Td px="0">
                                    <strong>
                                        {totalPlannedExpenses.toLocaleString()}
                                    </strong>
                                </Table.Td>
                                <Table.Td px="0">
                                    <strong>
                                        {totalActualExpenses.toLocaleString()}
                                    </strong>
                                </Table.Td>
                                <Table.Td px="0">
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
                                    <Table.Td key={day} px="0">
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
