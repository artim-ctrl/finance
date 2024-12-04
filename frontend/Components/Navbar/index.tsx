import { FC } from 'react'
import { Container, Group, Button } from '@mantine/core'
import AuthApi from 'Services/AuthApi'
import useUser from 'Hooks/useUser'

const Navbar: FC = () => {
    const { logout } = useUser()

    const handleLogout = async () => {
        await AuthApi.logout()

        logout()
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
                <Button variant="outline" color="red" onClick={handleLogout}>
                    Log Out
                </Button>
            </Group>
        </Container>
    )
}

export default Navbar
