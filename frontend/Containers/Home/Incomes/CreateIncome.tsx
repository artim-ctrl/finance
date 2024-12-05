import { FC, useMemo, useState } from 'react'
import {
    Modal,
    Button,
    Select,
    NumberInput,
    TextInput,
    Stack,
} from '@mantine/core'
import IncomeApi from 'Services/IncomeApi'

interface CreateIncomeProps {
    isOpen: boolean
    onClose: () => void
    existingCategories: { id: number; name: string }[]
    onIncomeCreated: () => void
}

export interface CreateIncomeData {
    name?: string
    category_id?: number
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

const CreateIncome: FC<CreateIncomeProps> = ({
    isOpen,
    onClose,
    existingCategories,
    onIncomeCreated,
}) => {
    const categoryOptions = useMemo<CategoryOption[]>(
        () =>
            existingCategories.map(({ id, name }) => ({
                value: String(id),
                label: name,
            })),
        [existingCategories],
    )

    const [categoryType, setCategoryType] = useState<'existing' | 'new'>(
        'existing',
    )
    const [selectedCategoryId, setSelectedCategoryId] = useState<string | null>(
        getDefaultCategoryId(categoryOptions),
    )
    const [newCategoryName, setNewCategoryName] = useState<string>('')
    const [amount, setAmount] = useState<number | string>('')

    const handleCreate = async () => {
        if (isNaN(Number(amount))) {
            return
        }

        const createData: CreateIncomeData = {
            amount: Number(amount),
        }

        if (categoryType === 'existing' && selectedCategoryId !== null) {
            createData.category_id = Number(selectedCategoryId)
        } else if (categoryType === 'new' && newCategoryName !== '') {
            createData.name = newCategoryName
        } else {
            return
        }

        await IncomeApi.create(createData)

        onIncomeCreated()
    }

    return (
        <Modal opened={isOpen} onClose={onClose} title="Добавить доход">
            <Stack>
                <Select
                    label="Тип категории"
                    value={categoryType}
                    onChange={(value) =>
                        setCategoryType(value as 'existing' | 'new')
                    }
                    data={[
                        { value: 'existing', label: 'Существующая категория' },
                        { value: 'new', label: 'Новая категория' },
                    ]}
                />
                {categoryType === 'existing' ? (
                    <Select
                        label="Выберите существующую категорию"
                        value={selectedCategoryId}
                        onChange={(value) => setSelectedCategoryId(value)}
                        data={categoryOptions}
                        allowDeselect={false}
                    />
                ) : (
                    <TextInput
                        label="Название новой категории"
                        value={newCategoryName}
                        onChange={(event) =>
                            setNewCategoryName(event.currentTarget.value)
                        }
                    />
                )}
                <NumberInput
                    label="Сумма"
                    value={amount}
                    onChange={setAmount}
                    min={0}
                    decimalScale={2}
                    step={0.01}
                />
                <Button onClick={handleCreate}>Создать</Button>
            </Stack>
        </Modal>
    )
}

export default CreateIncome
