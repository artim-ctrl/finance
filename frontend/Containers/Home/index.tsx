import { useMemo, useState } from 'react'
import { Container, Select, Text, Title } from '@mantine/core'
import dayjs from 'dayjs'
import advancedFormat from 'dayjs/plugin/advancedFormat'
import Incomes from './Incomes'
import ExpensesTable from './ExpensesTable'

dayjs.extend(advancedFormat)

const HomePage = () => {
    const [currentMonth, setCurrentMonth] = useState(dayjs().startOf('month'))

    const monthOptions = useMemo(() => {
        return Array.from({ length: 7 }, (_, i) => {
            const date = currentMonth.add(i - 3, 'month')

            return {
                value: date.format('YYYY-MM'),
                label: date.format('MMMM YYYY'),
            }
        })
    }, [currentMonth])

    return (
        <Container size="xl" mt="md" pb="xl">
            <Title order={1} ta="center">
                Budget Expenses
            </Title>
            <Text size="lg" c="dimmed" mt="sm" ta="center">
                This section displays your expenses by day of the month and your
                incomes by month.
            </Text>

            <Select
                value={currentMonth.format('YYYY-MM')}
                onChange={(value) => {
                    if (!value) return

                    setCurrentMonth(dayjs(value, 'YYYY-MM').startOf('month'))
                }}
                data={monthOptions}
                label="Select a month and year"
                mt="lg"
                allowDeselect={false}
                aria-label="Select a month"
            />

            <Incomes currentDate={currentMonth.toDate()} />

            <ExpensesTable currentMonth={currentMonth.toDate()} />
        </Container>
    )
}

export default HomePage
