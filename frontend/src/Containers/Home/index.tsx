import { FC, useEffect, useState } from 'react'
import {
    Container,
    Title,
    Table,
    Text,
    NumberInput,
    Select,
} from '@mantine/core'
import { format, getDaysInMonth, addMonths, startOfMonth } from 'date-fns'

type CategoryType = 'expenses' | 'incomes'

interface Category {
    name: string
    amount: number
    dailyExpenses: { [day: number]: number }
}

const initialExpenseCategories: Category[] = [
    { name: 'Бытовые нужды', amount: 4000, dailyExpenses: {} },
    { name: 'Гигиена и здоровье', amount: 5000, dailyExpenses: {} },
    { name: 'Квартплата', amount: 88000, dailyExpenses: {} },
    { name: 'Одежда и косметика', amount: 5000, dailyExpenses: {} },
    { name: 'Поездки (транспорт, такси)', amount: 800, dailyExpenses: {} },
    { name: 'Продукты питания', amount: 50000, dailyExpenses: {} },
    { name: 'Развлечения и подарки', amount: 5000, dailyExpenses: {} },
    { name: 'Связь (телефон, интернет)', amount: 2000, dailyExpenses: {} },
    { name: 'Спортзал', amount: 0, dailyExpenses: {} },
    { name: 'Коллекционирование', amount: 0, dailyExpenses: {} },
    { name: 'Коты', amount: 9000, dailyExpenses: {} },
    { name: 'Медицина', amount: 0, dailyExpenses: {} },
]

const initialIncomeCategories: Category[] = [
    { name: 'Зарплата', amount: 320000, dailyExpenses: {} },
    { name: 'Остаток с предыдущего месяца', amount: 80000, dailyExpenses: {} },
]

const initialCategories = {
    expenses: initialExpenseCategories,
    incomes: initialIncomeCategories,
}

const HomePage: FC = () => {
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
                Расходы бюджета
            </Title>
            <Text size="lg" c="dimmed" mt="sm" ta="center">
                В этом разделе отображаются все ваши категории расходов по дням
                месяца, а также категории доходов по месяцам.
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
                label="Выберите месяц и год"
                mt="lg"
                allowDeselect={false}
            />

            <Title order={2} mt="lg">
                Доходы
            </Title>
            <Table striped mt="sm">
                <Table.Thead>
                    <Table.Tr>
                        <Table.Th>Категория</Table.Th>
                        <Table.Th>Сумма (RSD)</Table.Th>
                    </Table.Tr>
                </Table.Thead>
                <Table.Tbody>
                    {incomeCategoriesData.map((category, categoryIndex) => (
                        <Table.Tr key={categoryIndex}>
                            <Table.Td>{category.name}</Table.Td>
                            <Table.Td>
                                <NumberInput
                                    value={category.amount}
                                    onBlur={(event) => {
                                        const value =
                                            parseFloat(event.target.value) || 0
                                        setIncomeCategoriesData(
                                            (prevCategories) => {
                                                const updatedCategories = [
                                                    ...prevCategories,
                                                ]
                                                updatedCategories[
                                                    categoryIndex
                                                ] = {
                                                    ...updatedCategories[
                                                        categoryIndex
                                                    ],
                                                    amount: value,
                                                }
                                                saveCategoriesDataForMonth(
                                                    currentMonth,
                                                    updatedCategories,
                                                    'incomes',
                                                )
                                                return updatedCategories
                                            },
                                        )
                                    }}
                                    min={0}
                                    decimalScale={2}
                                    step={0.01}
                                />
                            </Table.Td>
                        </Table.Tr>
                    ))}
                    <Table.Tr>
                        <Table.Td>
                            <strong>Общий доход</strong>
                        </Table.Td>
                        <Table.Td>
                            <strong>
                                {totalPlannedIncome.toLocaleString()} RSD
                            </strong>
                        </Table.Td>
                    </Table.Tr>
                </Table.Tbody>
            </Table>

            <Title order={2} mt="lg">
                Расходы
            </Title>
            <Table.ScrollContainer minWidth={100}>
                <Table striped mt="lg">
                    <Table.Thead>
                        <Table.Tr>
                            <Table.Th>Категория</Table.Th>
                            <Table.Th>Предполагаемые расходы (RSD)</Table.Th>
                            <Table.Th>Фактические расходы (RSD)</Table.Th>
                            <Table.Th>Отклонение от плана (RSD)</Table.Th>
                            {daysInMonth.map((day) => (
                                <Table.Th key={day}>День {day}</Table.Th>
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
                                                placeholder="Расходы"
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
                                <strong>Итоги</strong>
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
                Отчет
            </Title>
            <Table striped mt="sm">
                <Table.Thead>
                    <Table.Tr>
                        <Table.Th>Описание</Table.Th>
                        <Table.Th>Значение (RSD)</Table.Th>
                    </Table.Tr>
                </Table.Thead>
                <Table.Tbody>
                    <Table.Tr>
                        <Table.Td>Бюджет на месяц</Table.Td>
                        <Table.Td>{totalPlannedIncome.toFixed(2)}</Table.Td>
                    </Table.Tr>
                    <Table.Tr>
                        <Table.Td>Расходы за месяц</Table.Td>
                        <Table.Td>{totalActualExpenses.toFixed(2)}</Table.Td>
                    </Table.Tr>
                    <Table.Tr>
                        <Table.Td>Сальдо (разница)</Table.Td>
                        <Table.Td
                            style={{ color: saldo < 0 ? 'red' : 'green' }}
                        >
                            {saldo.toFixed(2)}
                        </Table.Td>
                    </Table.Tr>
                    <Table.Tr>
                        <Table.Td>Расходы без накоплений</Table.Td>
                        <Table.Td>{totalActualExpenses.toFixed(2)}</Table.Td>
                    </Table.Tr>
                    <Table.Tr>
                        <Table.Td>План расходов</Table.Td>
                        <Table.Td>{totalPlannedExpenses.toFixed(2)}</Table.Td>
                    </Table.Tr>
                    <Table.Tr>
                        <Table.Td>Отход от плана</Table.Td>
                        <Table.Td>{totalDeviation.toFixed(2)}</Table.Td>
                    </Table.Tr>
                    <Table.Tr>
                        <Table.Td>Ожидаемый остаток</Table.Td>
                        <Table.Td>{expectedBalance.toFixed(2)}</Table.Td>
                    </Table.Tr>
                </Table.Tbody>
            </Table>
        </Container>
    )
}

export default HomePage
