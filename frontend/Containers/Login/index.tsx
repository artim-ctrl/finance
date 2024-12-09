import { useState } from 'react'
import {
    Button,
    TextInput,
    PasswordInput,
    Container,
    Text,
    Notification,
    Title,
} from '@mantine/core'
import { FormErrors, useForm } from '@mantine/form'
import AuthApi from 'Services/AuthApi'
import ROUTES from 'Constants/routes'
import { Link } from 'react-router'
import useUser from 'Hooks/useUser'
import { AxiosError } from 'axios'

const Login = () => {
    const { login } = useUser()
    const [error, setError] = useState<string | null>(null)
    const [isLoading, setIsLoading] = useState<boolean>(false)

    const form = useForm({
        initialValues: {
            email: '',
            password: '',
        },
        validate: {
            email: (value) =>
                !value.trim()
                    ? 'Please enter your email'
                    : !/^\S+@\S+\.\S+$/.test(value)
                      ? 'Please enter a valid email address'
                      : null,
            password: (value) =>
                !value.trim()
                    ? 'Please enter your password'
                    : value.trim().length < 6
                      ? 'Password should be at least 6 characters long'
                      : null,
        },
    })

    const handleSubmit = async (values: {
        email: string
        password: string
    }) => {
        setIsLoading(true)
        setError(null)

        try {
            login(await AuthApi.login(values))
        } catch (error) {
            if (
                error instanceof AxiosError &&
                error.status === 422 &&
                error.response !== undefined
            ) {
                form.setErrors(error.response.data as FormErrors)
            } else {
                setError((error as Error).message || 'Login failed')
            }
        } finally {
            setIsLoading(false)
        }
    }

    return (
        <Container size="xs" style={{ maxWidth: 400, paddingTop: 20 }}>
            <Title order={2} style={{ marginBottom: 20 }}>
                Login
            </Title>

            {error !== null && (
                <Notification color="red" onClose={() => setError(null)}>
                    {error}
                </Notification>
            )}

            <form onSubmit={form.onSubmit(handleSubmit)}>
                <TextInput
                    label="Email"
                    placeholder="Your email"
                    {...form.getInputProps('email')}
                    required
                />
                <PasswordInput
                    label="Password"
                    placeholder="Your password"
                    {...form.getInputProps('password')}
                    required
                    style={{ marginTop: 20 }}
                />
                <Button
                    type="submit"
                    fullWidth
                    style={{ marginTop: 20 }}
                    loading={isLoading}
                    disabled={isLoading}
                >
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
