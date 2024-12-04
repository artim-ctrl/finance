import { useState, useEffect, FC, ReactNode } from 'react'
import AuthApi from 'Services/AuthApi'
import { useNavigate } from 'react-router'
import ROUTES from 'Constants/routes'
import { UserContext, User } from 'Contexts'

export const UserProvider: FC<{ children: ReactNode }> = ({ children }) => {
    const [user, setUser] = useState<User | null>(null)
    const [isLoading, setIsLoading] = useState(true)
    const navigate = useNavigate()

    useEffect(() => {
        AuthApi.profile().finally(() => setIsLoading(false))
    }, [])

    const login = (user: User) => {
        setUser(user)

        navigate(ROUTES.HOME)
    }

    const register = login

    const logout = () => {
        setUser(null)

        navigate(ROUTES.LOGIN)
    }

    return (
        <UserContext.Provider
            value={{ user, isLoading, login, register, logout }}
        >
            {children}
        </UserContext.Provider>
    )
}
