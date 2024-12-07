import { ReactNode } from 'react'
import Navbar from 'Components/Navbar'
import useUser from 'Hooks/useUser'

interface NavbarProviderProps {
    children: ReactNode
}

const NavbarProvider = ({ children }: NavbarProviderProps) => {
    const { user } = useUser()

    const isAuthenticated = user !== null

    return (
        <>
            {isAuthenticated && <Navbar />}
            {children}
        </>
    )
}

export default NavbarProvider
