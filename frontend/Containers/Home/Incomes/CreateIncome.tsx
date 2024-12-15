import { useMemo, useState } from 'react'
import {
    Modal,
    Button,
    Select,
    NumberInput,
    TextInput,
    Stack,
} from '@mantine/core'
import { FormErrors, useForm } from '@mantine/form'
import IncomeApi from 'Services/IncomeApi'
import { AxiosError } from 'axios'
import { showError } from 'Services/notify'

interface CreateIncomeProps {
    currentDate: Date
    isOpen: boolean
    onClose: () => void
    existingCategories: { id: number; name: string }[]
    onIncomeCreated: () => void
}

export interface CreateIncomeData {
    categoryName?: string
    categoryId?: number
    amount: number
}

interface CategoryOption {
    label: string
    value: string
}

const getDefaultCategoryId = (options: CategoryOption[]): string | null => {
    if (options.length === 0) {
        return null
    }

    return options[0].value
}

const CreateIncome = ({
    currentDate,
    isOpen,
    onClose,
    existingCategories,
    onIncomeCreated,
}: CreateIncomeProps) => {
    const [isLoading, setIsLoading] = useState<boolean>(false)

    const categoryOptions = useMemo<CategoryOption[]>(
        () =>
            existingCategories.map(({ id, name }) => ({
                value: String(id),
                label: name,
            })),
        [existingCategories],
    )

    const form = useForm({
        initialValues: {
            categoryType: 'existing' as 'existing' | 'new',
            categoryId: getDefaultCategoryId(categoryOptions),
            categoryName: '',
            amount: '',
        },
        validate: {
            amount: (value) =>
                isNaN(Number(value)) || Number(value) <= 0
                    ? 'Please enter a valid amount'
                    : null,
            categoryName: (value, values) =>
                values.categoryType === 'new' && !value.trim()
                    ? 'Please provide a name for the new category'
                    : null,
            categoryId: (value, values) =>
                values.categoryType === 'existing' && !value
                    ? 'Please select an existing category'
                    : null,
        },
    })

    const handleCreate = async (values: typeof form.values) => {
        setIsLoading(true)

        const { amount, categoryType, categoryId, categoryName } = values
        const parsedAmount = Number(amount)

        if (isNaN(parsedAmount)) {
            setIsLoading(false)
            return
        }

        const createData: CreateIncomeData = {
            amount: parsedAmount,
        }

        if (categoryType === 'existing' && categoryId !== null) {
            createData.categoryId = Number(categoryId)
        } else if (categoryType === 'new' && categoryName.trim() !== '') {
            createData.categoryName = categoryName.trim()
        } else {
            setIsLoading(false)
            return
        }

        try {
            await IncomeApi.create(
                currentDate.getFullYear(),
                currentDate.getMonth() + 1,
                createData,
            )

            form.reset()
            onIncomeCreated()
        } catch (error) {
            if (
                error instanceof AxiosError &&
                error.status === 422 &&
                error.response !== undefined
            ) {
                form.setErrors(error.response.data as FormErrors)
            } else {
                showError((error as Error).message || 'Creation failed')
            }
        } finally {
            setIsLoading(false)
        }
    }

    return (
        <Modal opened={isOpen} onClose={onClose} title="Add Income">
            <form onSubmit={form.onSubmit(handleCreate)}>
                <Stack>
                    <Select
                        label="Category Type"
                        data={[
                            { value: 'existing', label: 'Existing Category' },
                            { value: 'new', label: 'New Category' },
                        ]}
                        {...form.getInputProps('categoryType')}
                    />

                    {form.values.categoryType === 'existing' ? (
                        <Select
                            label="Select Existing Category"
                            data={categoryOptions}
                            {...form.getInputProps('categoryId')}
                            allowDeselect={false}
                        />
                    ) : (
                        <TextInput
                            label="New Category Name"
                            placeholder="Enter new category name"
                            {...form.getInputProps('categoryName')}
                        />
                    )}

                    <NumberInput
                        label="Amount"
                        placeholder="0.00"
                        min={0}
                        decimalScale={2}
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

export default CreateIncome
