import { useState } from 'react'
import {
    Button,
    TextInput,
    PasswordInput,
    Container,
    Text,
    Select,
    Title,
} from '@mantine/core'
import { FormErrors, useForm } from '@mantine/form'
import AuthApi from 'Services/AuthApi'
import ROUTES from 'Constants/routes'
import { Link } from 'react-router'
import useUser from 'Hooks/useUser'
import { AxiosError } from 'axios'
import { showError } from 'Services/notify'

const Registration = () => {
    const { register } = useUser()
    const [isLoading, setIsLoading] = useState<boolean>(false)

    const form = useForm({
        initialValues: {
            name: '',
            currency: 'EUR',
            email: '',
            password: '',
        },
        validate: {
            name: (value) => (!value.trim() ? 'Please enter your name' : null),
            currency: (value) => (!value ? 'Please select a currency' : null),
            email: (value) =>
                !value.trim()
                    ? 'Please enter your email'
                    : !/^\S+@\S+\.\S+$/.test(value)
                      ? 'Please enter a valid email address'
                      : null,
            password: (value) =>
                !value.trim()
                    ? 'Please enter a password'
                    : value.trim().length < 6
                      ? 'Password should be at least 6 characters long'
                      : null,
        },
    })

    const handleSubmit = async (values: {
        name: string
        currency: string
        email: string
        password: string
    }) => {
        setIsLoading(true)

        try {
            register(await AuthApi.register(values))
        } catch (error) {
            if (
                error instanceof AxiosError &&
                error.status === 422 &&
                error.response !== undefined
            ) {
                form.setErrors(error.response.data as FormErrors)
            } else {
                showError((error as Error).message || 'Registration failed')
            }
        } finally {
            setIsLoading(false)
        }
    }

    return (
        <Container size="xs" style={{ maxWidth: 400, paddingTop: 20 }}>
            <Title order={2} style={{ marginBottom: 20 }}>
                Sign Up
            </Title>

            <form onSubmit={form.onSubmit(handleSubmit)}>
                <TextInput
                    label="Name"
                    placeholder="Your name"
                    {...form.getInputProps('name')}
                    required
                />
                <Select
                    label="Currency"
                    data={['USD', 'EUR', 'RSD']}
                    {...form.getInputProps('currency')}
                    required
                    style={{ marginTop: 20 }}
                    allowDeselect={false}
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
                <Button
                    type="submit"
                    fullWidth
                    style={{ marginTop: 20 }}
                    loading={isLoading}
                    disabled={isLoading}
                >
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
