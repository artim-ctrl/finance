import { PieChart } from '@mantine/charts'
import {
    Container,
    Text,
    Group,
    Chip,
    NumberFormatter,
    Flex,
} from '@mantine/core'
import { useEffect, useState } from 'react'
import PieApi from 'Services/PieApi'
import useUser from 'Hooks/useUser'
import { User, UserContextProps } from 'Contexts'
import { DatePicker } from '@mantine/dates'
import dayjs from 'dayjs'

interface Expense {
    id: number
    category: string
    amount: number
}

const DATE_TEMPLATE = {
    THIS_MONTH: {
        id: 0,
        name: 'This Month',
    },
    LAST_MONTH: {
        id: 1,
        name: 'Last Month',
    },
    PREVIOUS_MONTH: {
        id: 2,
        name: 'Previous Month',
    },
    CUSTOM: {
        id: 3,
        name: 'Custom',
    },
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

    const [dateTemplate, setDateTemplate] = useState(
        String(DATE_TEMPLATE.THIS_MONTH.id),
    )
    const [customDate, setCustomDate] = useState<[Date | null, Date | null]>([
        dayjs().startOf('month').toDate(),
        dayjs().endOf('month').toDate(),
    ])
    const [customDatePage, setCustomDatePage] = useState(new Date())
    const [selectedIncomes, setSelectedIncomes] = useState<string[]>([])
    const [expensesData, setExpensesData] = useState<Expense[]>([])
    const [isLoading, setIsLoading] = useState<boolean>(true)
    const [error, setError] = useState<string | null>(null)

    useEffect(() => {
        let customDate: [Date, Date] | null = null
        switch (Number(dateTemplate)) {
            case DATE_TEMPLATE.THIS_MONTH.id:
                customDate = [
                    dayjs().startOf('month').toDate(),
                    dayjs().endOf('month').toDate(),
                ]
                break
            case DATE_TEMPLATE.LAST_MONTH.id:
                customDate = [
                    dayjs().subtract(1, 'month').toDate(),
                    dayjs().toDate(),
                ]
                break
            case DATE_TEMPLATE.PREVIOUS_MONTH.id:
                customDate = [
                    dayjs().subtract(1, 'month').startOf('month').toDate(),
                    dayjs().subtract(1, 'month').endOf('month').toDate(),
                ]
                break
        }

        if (customDate !== null) {
            setCustomDate(customDate ?? [null, null])
            setCustomDatePage(
                new Date(customDate[0].getFullYear(), customDate[0].getMonth()),
            )
        }
    }, [dateTemplate])

    useEffect(() => {
        const fetchData = async () => {
            try {
                const expensesData = (await PieApi.getExpenses(
                    dayjs(customDate[0]).format('YYYY-MM-DD'),
                    dayjs(customDate[1]).format('YYYY-MM-DD'),
                )) as Expense[]

                setExpensesData(expensesData)

                setSelectedIncomes(expensesData.map(({ id }) => String(id)))
            } catch {
                setError('Error fetching expenses data')
            } finally {
                setIsLoading(false)
            }
        }

        if (customDate[0] !== null && customDate[1] !== null) {
            fetchData()
        }
    }, [customDate])

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

            <Flex direction="column" align="center">
                <Chip.Group
                    value={dateTemplate}
                    onChange={(value) => setDateTemplate(value as string)}
                >
                    <Group justify="center">
                        {Object.entries(DATE_TEMPLATE).map(
                            ([key, { id, name }]) => (
                                <Chip
                                    value={String(id)}
                                    variant="outline"
                                    key={key}
                                >
                                    {name}
                                </Chip>
                            ),
                        )}
                    </Group>
                </Chip.Group>

                {dateTemplate === String(DATE_TEMPLATE.CUSTOM.id) && (
                    <DatePicker
                        mt="sm"
                        type="range"
                        numberOfColumns={2}
                        date={customDatePage}
                        onDateChange={setCustomDatePage}
                        value={customDate}
                        onChange={setCustomDate}
                    />
                )}
            </Flex>

            <Chip.Group
                multiple
                value={selectedIncomes}
                onChange={setSelectedIncomes}
            >
                <Group justify="center" mt="md">
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
                </Group>
            </Chip.Group>

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
