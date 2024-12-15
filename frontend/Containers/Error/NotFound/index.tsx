import { Container, Text, Button, Group } from '@mantine/core'
import { useNavigate } from 'react-router'
import ROUTES from 'Constants/routes'

const NotFound = () => {
    const navigate = useNavigate()

    return (
        <Container size="sm" ta="center" mt="xl">
            <Text size="xl" fw={700} mb="md">
                404 — Page Not Found
            </Text>
            <Text size="md" c="dimmed" mb="md">
                The page you are looking for doesn’t exist or has been moved.
            </Text>
            <Group justify="center">
                <Button onClick={() => navigate(ROUTES.HOME)} variant="filled">
                    Go to Home
                </Button>
            </Group>
        </Container>
    )
}

export default NotFound
