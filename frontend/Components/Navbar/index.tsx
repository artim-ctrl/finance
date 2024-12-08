import { Container, Group, Button, Select } from '@mantine/core'
import AuthApi from 'Services/AuthApi'
import useUser from 'Hooks/useUser'
import { User, UserContextProps } from 'Contexts'
import { useState } from 'react'

const Navbar = () => {
    const [isLoading, setIsLoading] = useState(false)

    const { user, updateCurrency, logout } = useUser() as UserContextProps & {
        user: User
    }

    const handleLogout = async () => {
        await AuthApi.logout()

        logout()
    }

    const handleChangeCurrency = async (currency: string) => {
        setIsLoading(true)

        try {
            await AuthApi.update({ currency })

            updateCurrency(currency)
        } finally {
            setIsLoading(false)
        }
    }

    return (
        <Container
            size="xl"
            p="md"
            style={{
                display: 'flex',
                justifyContent: 'space-between',
                alignItems: 'center',
                borderBottom: '1px solid #eaeaea',
            }}
        >
            <div>
                <strong>Budget Tracker</strong>
            </div>

            <Group>
                <Select
                    value={user?.currency.currency}
                    onChange={(value) => handleChangeCurrency(value as string)}
                    data={['USD', 'EUR', 'RSD']}
                    allowDeselect={false}
                    aria-label="Select a currency"
                    disabled={isLoading}
                />

                <Button variant="outline" color="red" onClick={handleLogout}>
                    Log Out
                </Button>
            </Group>
        </Container>
    )
}

export default Navbar
