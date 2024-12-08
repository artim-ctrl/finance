import { createContext } from 'react'

export interface User {
    id: number
    name: string
    email: string
    currency: { currency: string }
}

export interface UserContextProps {
    user: User | null
    isLoading: boolean
    logout: () => void
    login: (user: User) => void
    register: (user: User) => void
    updateCurrency: (currency: string) => void
}

export const UserContext = createContext<UserContextProps | undefined>(
    undefined,
)
