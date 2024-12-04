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

const Registration = () => {
    const { register } = useUser()
    const [error, setError] = useState<string | null>(null)
    const form = useForm({
        initialValues: {
            name: '',
            email: '',
            password: '',
        },
    })

    const handleSubmit = async (values: {
        name: string
        email: string
        password: string
    }) => {
        try {
            register(await AuthApi.register(values))
        } catch (error) {
            setError((error as Error).message || 'Registration failed')
        }
    }

    return (
        <Container size="xs" style={{ maxWidth: 400 }}>
            <h2>Sign Up</h2>
            {error && (
                <Notification color="red" onClose={() => setError(null)}>
                    {error}
                </Notification>
            )}

            <form onSubmit={form.onSubmit(handleSubmit)}>
                <TextInput
                    label="Name"
                    placeholder="Your name"
                    {...form.getInputProps('name')}
                    required
                />
                <TextInput
                    label="Email"
                    placeholder="Your email"
                    {...form.getInputProps('email')}
                    required
                    style={{ marginTop: 20 }}
                />
                <PasswordInput
                    label="Password"
                    placeholder="Your password"
                    {...form.getInputProps('password')}
                    required
                    style={{ marginTop: 20 }}
                />
                <Button type="submit" fullWidth style={{ marginTop: 20 }}>
                    Sign Up
                </Button>
            </form>

            <Text ta="center" style={{ marginTop: 20 }}>
                Already have an account? <Link to={ROUTES.LOGIN}>Log In</Link>
            </Text>
        </Container>
    )
}

export default Registration
