import { useState } from 'react'
import {
    Button,
    TextInput,
    PasswordInput,
    Container,
    Text,
    Notification,
} from '@mantine/core'
import { useForm } from '@mantine/form'
import AuthApi from 'Services/AuthApi'
import ROUTES from 'Constants/routes'
import { Link } from 'react-router'
import useUser from 'Hooks/useUser'

const Login = () => {
    const { login } = useUser()
    const [error, setError] = useState<string | null>(null)
    const form = useForm({
        initialValues: {
            email: '',
            password: '',
        },
    })

    const handleSubmit = async (values: {
        email: string
        password: string
    }) => {
        try {
            login(await AuthApi.login(values))
        } catch (error) {
            setError((error as Error).message || 'Login failed')
        }
    }

    return (
        <Container size="xs" style={{ maxWidth: 400 }}>
            <h2>Login</h2>
            {error && (
                <Notification color="red" onClose={() => setError(null)}>
                    {error}
                </Notification>
            )}

            <form onSubmit={form.onSubmit(handleSubmit)}>
                <TextInput
                    label="Email"
                    placeholder="Your email"
                    key={form.key('email')}
                    {...form.getInputProps('email')}
                    required
                />
                <PasswordInput
                    label="Password"
                    placeholder="Your password"
                    key={form.key('password')}
                    {...form.getInputProps('password')}
                    required
                    style={{ marginTop: 20 }}
                />
                <Button type="submit" fullWidth style={{ marginTop: 20 }}>
                    Log In
                </Button>
            </form>

            <Text ta="center" style={{ marginTop: 20 }}>
                Don’t have an account? <Link to={ROUTES.REGISTER}>Sign Up</Link>
            </Text>
        </Container>
    )
}

export default Login
