import { ReactNode } from 'react'
import { Navigate, useLocation } from 'react-router'
import useUser from 'Hooks/useUser'
import ROUTES from 'Constants/routes'
import { Loader } from '@mantine/core'

interface AuthGuardProps {
    children: ReactNode
    isAuthRequired: boolean
}

const AuthGuard = ({ children, isAuthRequired }: AuthGuardProps) => {
    const { user, isLoading } = useUser()
    const location = useLocation()

    if (isLoading) {
        return (
            <Loader
                type="dots"
                size="lg"
                style={{ width: '100%', height: '100%', background: 'white' }}
            />
        )
    }

    const isAuthenticated = user !== null

    if (isAuthRequired && !isAuthenticated) {
        return <Navigate to={ROUTES.LOGIN} state={{ from: location }} />
    }

    if (!isAuthRequired && isAuthenticated) {
        return <Navigate to={ROUTES.HOME} state={{ from: location }} />
    }

    return children
}

export default AuthGuard
