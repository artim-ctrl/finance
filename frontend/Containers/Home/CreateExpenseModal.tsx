import { useState } from 'react'
import { Modal, TextInput, NumberInput, Button, Stack } from '@mantine/core'
import { DateInput } from '@mantine/dates'
import { useForm } from '@mantine/form'
import ExpenseApi from 'Services/ExpenseApi'
import dayjs from 'dayjs'
import { showError } from 'Services/notify'

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
    const [isLoading, setIsLoading] = useState(false)

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
        setIsLoading(true)
        const { date, categoryName, amount } = values
        const parsedAmount = Number(amount)
        if (isNaN(parsedAmount) || parsedAmount <= 0) {
            setIsLoading(false)
            return
        }

        const createData: CreateExpenseData = {
            date: dayjs(date).format('YYYY-MM-DD'),
            amount: parsedAmount,
            categoryName: categoryName.trim(),
        }

        try {
            await ExpenseApi.create(createData)
            form.reset()
            onExpenseCreated()
            onClose()
        } catch (e) {
            showError((e as Error).message || 'Creation failed')
        } finally {
            setIsLoading(false)
        }
    }

    return (
        <Modal opened={isOpen} onClose={onClose} title="Add Expense">
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

                    <Button
                        type="submit"
                        loading={isLoading}
                        disabled={isLoading}
                    >
                        Create
                    </Button>
                </Stack>
            </form>
        </Modal>
    )
}

export default CreateExpenseModal
