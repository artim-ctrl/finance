import { useState } from 'react'
import {
    Modal,
    TextInput,
    NumberInput,
    Button,
    Stack,
    Notification,
} from '@mantine/core'
import { DateInput } from '@mantine/dates'
import { useForm } from '@mantine/form'
import ExpenseApi from 'Services/ExpenseApi'
import { format } from 'date-fns'

interface CreateExpenseModalProps {
    isOpen: boolean
    onClose: () => void
    onExpenseCreated: () => void
}

export interface CreateExpenseData {
    date: string
    categoryName: string
    amount: number
}

const CreateExpenseModal = ({
    isOpen,
    onClose,
    onExpenseCreated,
}: CreateExpenseModalProps) => {
    const [error, setError] = useState<string | null>(null)
    const [loading, setLoading] = useState(false)

    const form = useForm({
        initialValues: {
            categoryName: '',
            date: new Date(),
            amount: '',
        },
        validate: {
            date: (value) => (!value ? 'Please select a date' : null),
            amount: (value) =>
                isNaN(Number(value)) || Number(value) <= 0
                    ? 'Please enter a valid amount'
                    : null,
            categoryName: (value) =>
                !value.trim() ? 'Please provide a category name' : null,
        },
    })

    const handleCreate = async (values: typeof form.values) => {
        setLoading(true)
        setError(null)
        const { date, categoryName, amount } = values
        const parsedAmount = Number(amount)
        if (isNaN(parsedAmount) || parsedAmount <= 0) {
            setLoading(false)
            return
        }

        const createData: CreateExpenseData = {
            date: format(date, 'yyyy-MM-dd'),
            amount: parsedAmount,
            categoryName: categoryName.trim(),
        }

        try {
            await ExpenseApi.create(createData)
            form.reset()
            onExpenseCreated()
            onClose()
        } catch (e) {
            setError((e as Error).message || 'Creation failed')
        } finally {
            setLoading(false)
        }
    }

    return (
        <Modal opened={isOpen} onClose={onClose} title="Add Expense">
            {error !== null && (
                <Notification color="red" onClose={() => setError(null)}>
                    {error}
                </Notification>
            )}

            <form onSubmit={form.onSubmit(handleCreate)}>
                <Stack>
                    <TextInput
                        label="New Category Name"
                        placeholder="Enter new category name"
                        {...form.getInputProps('categoryName')}
                        required
                    />

                    <DateInput
                        label="Date"
                        {...form.getInputProps('date')}
                        placeholder="Select a date"
                        required
                    />

                    <NumberInput
                        label="Amount"
                        placeholder="0.00"
                        min={0}
                        step={0.01}
                        {...form.getInputProps('amount')}
                    />

                    <Button type="submit" loading={loading} disabled={loading}>
                        Create
                    </Button>
                </Stack>
            </form>
        </Modal>
    )
}

export default CreateExpenseModal
