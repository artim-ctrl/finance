import { FC, ReactNode } from 'react'
import { Navigate, useLocation } from 'react-router'
import useUser from 'Hooks/useUser'
import { Loader } from '@mantine/core'
import ROUTES from 'Constants/routes'
import Navbar from 'Components/Navbar'

interface AuthGuardProps {
    children: ReactNode
    isAuthRequired: boolean
}

const AuthGuard: FC<AuthGuardProps> = ({ children, isAuthRequired }) => {
    const { user, isLoading } = useUser()
    const location = useLocation()

    if (isLoading) {
        return (
            <Loader
                type="dots"
                size="lg"
                style={{ width: '100%', height: '100%' }}
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

    return (
        <>
            {isAuthenticated && <Navbar />}
            {children}
        </>
    )
}

export default AuthGuard
