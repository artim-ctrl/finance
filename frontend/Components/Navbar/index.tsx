import {
    Container,
    Group,
    Button,
    Select,
    ActionIcon,
    useMantineColorScheme,
} from '@mantine/core'
import { useNavigate } from 'react-router'
import AuthApi from 'Services/AuthApi'
import useUser from 'Hooks/useUser'
import { User, UserContextProps } from 'Contexts'
import { useState } from 'react'
import CURRENCIES from 'Constants/currencies'
import { IconMoon, IconSun } from '@tabler/icons-react'

const Navbar = () => {
    const [isLoading, setIsLoading] = useState(false)
    const { colorScheme, toggleColorScheme } = useMantineColorScheme()
    const navigate = useNavigate()

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
            <div onClick={() => navigate('/')} style={{ cursor: 'pointer' }}>
                <strong>Budget Tracker</strong>
            </div>

            <Group>
                <Button variant="outline" onClick={() => navigate('/summary')}>
                    Go to Summary
                </Button>

                <Button variant="outline" onClick={() => navigate('/pie')}>
                    Go to Pie
                </Button>

                <Button
                    variant="outline"
                    onClick={() => navigate('/charts')}
                    style={{ marginRight: 20 }}
                >
                    Go to Charts
                </Button>

                <ActionIcon
                    variant="light"
                    size="lg"
                    onClick={() => toggleColorScheme()}
                    aria-label="Toggle color scheme"
                >
                    {colorScheme === 'dark' ? (
                        <IconSun stroke={1.5} />
                    ) : (
                        <IconMoon stroke={1.5} />
                    )}
                </ActionIcon>

                <Select
                    value={user?.currency.currency}
                    onChange={(value) => handleChangeCurrency(value as string)}
                    data={CURRENCIES}
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
