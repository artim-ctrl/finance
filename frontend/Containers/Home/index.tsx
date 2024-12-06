import { useState } from 'react'
import { Container, Select, Text, Title } from '@mantine/core'
import { addMonths, format, startOfMonth } from 'date-fns'
import Incomes from './Incomes'
import ExpensesTable from './ExpensesTable'

const HomePage = () => {
    const [currentMonth, setCurrentMonth] = useState<Date>(
        startOfMonth(new Date()),
    )

    const monthOptions = Array.from({ length: 7 }, (_, i) => {
        const date = addMonths(currentMonth, i - 3)

        return {
            value: format(date, 'yyyy-MM'),
            label: format(date, 'MMMM yyyy'),
        }
    })

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

            <ExpensesTable currentMonth={currentMonth} />
        </Container>
    )
}

export default HomePage
