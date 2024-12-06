import { useEffect, useState } from 'react'
import {
    Container,
    NumberInput,
    Select,
    Table,
    Text,
    Title,
} from '@mantine/core'
import { addMonths, format, getDaysInMonth, startOfMonth } from 'date-fns'
import Incomes from './Incomes'

type CategoryType = 'expenses' | 'incomes'

interface Category {
    name: string
    amount: number
    dailyExpenses: { [day: number]: number }
}

const initialExpenseCategories: Category[] = [
    { name: 'Household needs', amount: 4000, dailyExpenses: {} },
    { name: 'Hygiene and health', amount: 5000, dailyExpenses: {} },
    { name: 'Utilities', amount: 88000, dailyExpenses: {} },
    { name: 'Clothes and cosmetics', amount: 5000, dailyExpenses: {} },
    { name: 'Travel (transportation, taxi)', amount: 800, dailyExpenses: {} },
    { name: 'Groceries', amount: 50000, dailyExpenses: {} },
    { name: 'Entertainment and gifts', amount: 5000, dailyExpenses: {} },
    {
        name: 'Communication (phone, internet)',
        amount: 2000,
        dailyExpenses: {},
    },
    { name: 'Gym', amount: 0, dailyExpenses: {} },
    { name: 'Collecting', amount: 0, dailyExpenses: {} },
    { name: 'Cats', amount: 9000, dailyExpenses: {} },
    { name: 'Medical', amount: 0, dailyExpenses: {} },
]

const initialIncomeCategories: Category[] = [
    { name: 'Salary', amount: 320000, dailyExpenses: {} },
    { name: 'Remainder from previous month', amount: 80000, dailyExpenses: {} },
]

const initialCategories = {
    expenses: initialExpenseCategories,
    incomes: initialIncomeCategories,
}

const HomePage = () => {
    const [expenseCategoriesData, setExpenseCategoriesData] = useState<
        Category[]
    >(initialCategories.expenses)
    const [incomeCategoriesData, setIncomeCategoriesData] = useState<
        Category[]
    >(initialCategories.incomes)
    const [currentMonth, setCurrentMonth] = useState<Date>(
        startOfMonth(new Date()),
    )

    const getCategoriesDataForMonth = (
        month: Date,
    ): { expenses: Category[]; incomes: Category[] } => {
        const storedData = localStorage.getItem(format(month, 'yyyy-MM'))

        return storedData ? JSON.parse(storedData) : initialCategories
    }

    const saveCategoriesDataForMonth = (
        month: Date,
        data: Category[],
        type: CategoryType,
    ) => {
        const storedData = localStorage.getItem(format(month, 'yyyy-MM'))
        const parsedData = storedData
            ? JSON.parse(storedData)
            : { expenses: [], incomes: [] }
        parsedData[type] = data
        localStorage.setItem(
            format(month, 'yyyy-MM'),
            JSON.stringify(parsedData),
        )
    }

    useEffect(() => {
        const { expenses, incomes } = getCategoriesDataForMonth(currentMonth)

        setExpenseCategoriesData(expenses)
        setIncomeCategoriesData(incomes)
    }, [currentMonth])

    const handleDailyExpenseChange = (
        categoryIndex: number,
        day: number,
        value: number,
    ) => {
        setExpenseCategoriesData((prevCategories) => {
            const updatedCategories = [...prevCategories]
            updatedCategories[categoryIndex] = {
                ...updatedCategories[categoryIndex],
                dailyExpenses: {
                    ...updatedCategories[categoryIndex].dailyExpenses,
                    [day]: value,
                },
            }
            saveCategoriesDataForMonth(
                currentMonth,
                updatedCategories,
                'expenses',
            )
            return updatedCategories
        })
    }

    const daysInMonth = Array.from(
        { length: getDaysInMonth(currentMonth) },
        (_, i) => i + 1,
    )

    const calculateActualExpenses = (category: Category): number => {
        return Object.values(category.dailyExpenses).reduce(
            (sum, expense) => sum + expense,
            0,
        )
    }

    const calculateDailyTotal = (day: number): number => {
        return expenseCategoriesData.reduce(
            (sum, category) => sum + (category.dailyExpenses[day] || 0),
            0,
        )
    }

    const totalPlannedExpenses = expenseCategoriesData.reduce(
        (sum, cat) => sum + cat.amount,
        0,
    )
    const totalActualExpenses = expenseCategoriesData.reduce(
        (sum, cat) => sum + calculateActualExpenses(cat),
        0,
    )
    const totalDeviation = totalActualExpenses - totalPlannedExpenses

    const totalPlannedIncome = incomeCategoriesData.reduce(
        (sum, cat) => sum + cat.amount,
        0,
    )

    const monthOptions = Array.from({ length: 7 }, (_, i) => {
        const date = addMonths(currentMonth, i - 3)
        return {
            value: format(date, 'yyyy-MM'),
            label: format(date, 'MMMM yyyy'),
        }
    })

    const saldo = totalPlannedIncome - totalActualExpenses
    const expectedBalance = totalPlannedIncome - totalPlannedExpenses

    return (
        <Container size="xl" mt="md">
            <Title order={1} ta="center">
                Budget Expenses
            </Title>
            <Text size="lg" c="dimmed" mt="sm" ta="center">
                This section displays your expenses by day of the month and your
                incomes by month.
            </Text>

            <Select
                value={format(currentMonth, 'yyyy-MM')}
                onChange={(value) => {
                    const [year, month] = (value as string)
                        .split('-')
                        .map(Number)
                    setCurrentMonth(new Date(year, month - 1, 1))
                }}
                data={monthOptions}
                label="Select a month and year"
                mt="lg"
                allowDeselect={false}
            />

            <Incomes currentDate={currentMonth} />

            <Title order={2} mt="lg">
                Expenses
            </Title>
            <Table.ScrollContainer minWidth={100}>
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
                        {expenseCategoriesData.map(
                            (category, categoryIndex) => (
                                <Table.Tr key={categoryIndex}>
                                    <Table.Td>{category.name}</Table.Td>
                                    <Table.Td>
                                        {category.amount.toLocaleString()}
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
                                                    ) > category.amount
                                                        ? 'red'
                                                        : 'green',
                                                fontWeight: 'bold',
                                            }}
                                        >
                                            {(
                                                calculateActualExpenses(
                                                    category,
                                                ) - category.amount
                                            ).toFixed(2)}
                                        </span>
                                    </Table.Td>
                                    {daysInMonth.map((day) => (
                                        <Table.Td key={day}>
                                            <NumberInput
                                                value={
                                                    category.dailyExpenses[
                                                        day
                                                    ] || ''
                                                }
                                                onBlur={(event) => {
                                                    const value =
                                                        parseFloat(
                                                            event.target.value,
                                                        ) || 0
                                                    handleDailyExpenseChange(
                                                        categoryIndex,
                                                        day,
                                                        value,
                                                    )
                                                }}
                                                style={{ width: '100px' }}
                                                placeholder="Expenses"
                                                min={0}
                                                decimalScale={2}
                                                step={0.01}
                                            />
                                        </Table.Td>
                                    ))}
                                </Table.Tr>
                            ),
                        )}
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
                                        {calculateDailyTotal(day).toFixed(2)}
                                    </strong>
                                </Table.Td>
                            ))}
                        </Table.Tr>
                    </Table.Tbody>
                </Table>
            </Table.ScrollContainer>

            <Title order={2} mt="lg">
                Report
            </Title>
            <Table striped mt="sm">
                <Table.Thead>
                    <Table.Tr>
                        <Table.Th>Description</Table.Th>
                        <Table.Th>Value (RSD)</Table.Th>
                    </Table.Tr>
                </Table.Thead>
                <Table.Tbody>
                    <Table.Tr>
                        <Table.Td>Monthly budget</Table.Td>
                        <Table.Td>{totalPlannedIncome.toFixed(2)}</Table.Td>
                    </Table.Tr>
                    <Table.Tr>
                        <Table.Td>Monthly expenses</Table.Td>
                        <Table.Td>{totalActualExpenses.toFixed(2)}</Table.Td>
                    </Table.Tr>
                    <Table.Tr>
                        <Table.Td>Balance (difference)</Table.Td>
                        <Table.Td
                            style={{ color: saldo < 0 ? 'red' : 'green' }}
                        >
                            {saldo.toFixed(2)}
                        </Table.Td>
                    </Table.Tr>
                    <Table.Tr>
                        <Table.Td>Expenses without savings</Table.Td>
                        <Table.Td>{totalActualExpenses.toFixed(2)}</Table.Td>
                    </Table.Tr>
                    <Table.Tr>
                        <Table.Td>Expense plan</Table.Td>
                        <Table.Td>{totalPlannedExpenses.toFixed(2)}</Table.Td>
                    </Table.Tr>
                    <Table.Tr>
                        <Table.Td>Deviation from plan</Table.Td>
                        <Table.Td>{totalDeviation.toFixed(2)}</Table.Td>
                    </Table.Tr>
                    <Table.Tr>
                        <Table.Td>Expected remainder</Table.Td>
                        <Table.Td>{expectedBalance.toFixed(2)}</Table.Td>
                    </Table.Tr>
                </Table.Tbody>
            </Table>
        </Container>
    )
}

export default HomePage
