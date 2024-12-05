import { FC, useEffect, useState } from 'react'
import { Button, Table } from '@mantine/core'
import CreateIncome from './CreateIncome'
import IncomeApi from 'Services/IncomeApi'

interface IncomesProps {
    currentDate: Date
}

interface IncomeCategory {
    id: number
    name: string
    incomes?: { amount: number }[]
}

const Incomes: FC<IncomesProps> = ({ currentDate }) => {
    const [incomes, setIncomes] = useState<{ name: string; amount: number }[]>(
        [],
    )
    const [categories, setCategories] = useState<
        { id: number; name: string }[]
    >([])
    const [isCreateIncomeModalOpen, setIsCreateIncomeModalOpen] =
        useState(false)
    const [isLoading, setIsLoading] = useState(true)

    const loadCategories = async (currentDate: Date) => {
        const categories = (await IncomeApi.getCategories(
            currentDate.getFullYear(),
            currentDate.getMonth() + 1,
        )) as IncomeCategory[]

        setIncomes(
            categories.map((category) => ({
                name: category.name,
                amount:
                    category.incomes !== undefined
                        ? category.incomes.reduce(
                              (acc, { amount }) => amount + acc,
                              0,
                          )
                        : 0,
            })),
        )
        setCategories(categories.map(({ id, name }) => ({ id, name })))
    }

    useEffect(() => {
        loadCategories(currentDate).finally(() => setIsLoading(false))
    }, [currentDate])

    return (
        <div>
            <Button
                onClick={() => setIsCreateIncomeModalOpen(true)}
                mb="md"
                loading={isLoading && isCreateIncomeModalOpen}
            >
                Добавить доход
            </Button>
            <Table striped mt="sm">
                <Table.Thead>
                    <Table.Tr>
                        <Table.Th>Категория</Table.Th>
                        <Table.Th>Сумма (RSD)</Table.Th>
                    </Table.Tr>
                </Table.Thead>
                <Table.Tbody>
                    {incomes.map((income, index) => (
                        <Table.Tr key={index}>
                            <Table.Td>{income.name}</Table.Td>
                            <Table.Td>
                                {income.amount.toLocaleString()}
                            </Table.Td>
                        </Table.Tr>
                    ))}
                </Table.Tbody>
            </Table>
            {!isLoading && (
                <CreateIncome
                    isOpen={isCreateIncomeModalOpen}
                    onClose={() => setIsCreateIncomeModalOpen(false)}
                    existingCategories={categories}
                    onIncomeCreated={() => {
                        loadCategories(currentDate)

                        setIsCreateIncomeModalOpen(false)
                    }}
                />
            )}
        </div>
    )
}

export default Incomes
