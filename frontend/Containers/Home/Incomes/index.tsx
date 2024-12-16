import { useEffect, useState } from 'react'
import {
    Button,
    Table,
    Title,
    Flex,
    Box,
    LoadingOverlay,
    NumberInput,
} from '@mantine/core'
import CreateIncome from './CreateIncome'
import IncomeApi from 'Services/IncomeApi'
import useUser from 'Hooks/useUser'
import { User, UserContextProps } from 'Contexts'

interface IncomesProps {
    currentDate: Date
}

interface IncomeCategory {
    id: number
    name: string
    incomes?: { amount: number }[]
}

const Incomes = ({ currentDate }: IncomesProps) => {
    const [incomes, setIncomes] = useState<
        { name: string; amount: number; id: number }[]
    >([])
    const [categories, setCategories] = useState<
        { id: number; name: string }[]
    >([])
    const [isCreateIncomeModalOpen, setIsCreateIncomeModalOpen] =
        useState(false)
    const [isLoading, setIsLoading] = useState(true)
    const { user } = useUser() as UserContextProps & { user: User }

    const loadCategories = async (currentDate: Date) => {
        setIsLoading(true)

        const categories = (await IncomeApi.getCategories(
            currentDate.getFullYear(),
            currentDate.getMonth() + 1,
        )) as IncomeCategory[]

        setIncomes(
            categories.map((category) => ({
                id: category.id,
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

        setIsLoading(false)
    }

    useEffect(() => {
        loadCategories(currentDate)
    }, [currentDate])

    const handleAmountChange = async (id: number, newAmount: number) => {
        try {
            await IncomeApi.update(
                currentDate.getFullYear(),
                currentDate.getMonth() + 1,
                { category_id: id, amount: newAmount },
            )

            loadCategories(currentDate)
        } catch (error) {
            console.error('Failed to update amount:', error)
        }
    }

    return (
        <div>
            <Flex justify="space-between" align="center" mt="md">
                <Title order={2}>Incomes</Title>
                <Button
                    onClick={() => setIsCreateIncomeModalOpen(true)}
                    loading={isLoading && isCreateIncomeModalOpen}
                >
                    Add Income
                </Button>
            </Flex>
            <Box pos="relative">
                <LoadingOverlay
                    visible={isLoading}
                    zIndex={1000}
                    overlayProps={{ radius: 'sm', blur: 2 }}
                />

                <Table striped mt="sm">
                    <Table.Thead>
                        <Table.Tr>
                            <Table.Th>Category</Table.Th>
                            <Table.Th>
                                Amount ({user.currency.currency})
                            </Table.Th>
                        </Table.Tr>
                    </Table.Thead>
                    <Table.Tbody>
                        {incomes.map((income, index) => {
                            const save = (newAmount: number) => {
                                if (
                                    !isNaN(newAmount) &&
                                    newAmount !== income.amount
                                ) {
                                    handleAmountChange(income.id, newAmount)
                                }
                            }

                            return (
                                <Table.Tr key={index}>
                                    <Table.Td>{income.name}</Table.Td>
                                    <Table.Td>
                                        <NumberInput
                                            value={income.amount}
                                            onBlur={(e) =>
                                                save(parseFloat(e.target.value))
                                            }
                                            onKeyDown={(e) =>
                                                e.key === 'Enter' &&
                                                save(
                                                    parseFloat(
                                                        e.currentTarget.value,
                                                    ),
                                                )
                                            }
                                            placeholder="0.00"
                                            min={0}
                                            decimalScale={2}
                                            step={0.01}
                                            hideControls
                                            variant="unstyled"
                                            styles={{
                                                input: {
                                                    textAlign: 'center',
                                                },
                                            }}
                                        />
                                    </Table.Td>
                                </Table.Tr>
                            )
                        })}
                    </Table.Tbody>
                </Table>
            </Box>

            {!isLoading && (
                <CreateIncome
                    currentDate={currentDate}
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
