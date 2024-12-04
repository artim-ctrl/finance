import { createContext } from 'react'

export interface User {
    id: number
    name: string
    email: string
}

interface UserContextProps {
    user: User | null
    isLoading: boolean
    logout: () => void
    login: (user: User) => void
    register: (user: User) => void
}

export const UserContext = createContext<UserContextProps | undefined>(
    undefined,
)
