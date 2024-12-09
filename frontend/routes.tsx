import { Routes, Route } from 'react-router'
import Home from 'Containers/Home'
import About from 'Containers/About'
import Login from 'Containers/Login'
import Register from 'Containers/Register'
import AuthGuard from 'Components/AuthGuard'
import ROUTES from 'Constants/routes'
import NavbarProvider from 'Components/NavbarProvider'

const routesConfig = [
    {
        path: ROUTES.LOGIN,
        element: Login,
        isAuthRequired: false,
    },
    {
        path: ROUTES.REGISTER,
        element: Register,
        isAuthRequired: false,
    },
    {
        path: ROUTES.HOME,
        element: Home,
        isAuthRequired: true,
    },
    {
        path: ROUTES.ABOUT,
        element: About,
        isAuthRequired: true,
    },
]

const RoutesConfig = () => {
    return (
        <Routes>
            {routesConfig.map(
                ({ path, element: Component, isAuthRequired }) => (
                    <Route
                        key={path}
                        path={path}
                        element={
                            <AuthGuard isAuthRequired={isAuthRequired}>
                                <NavbarProvider>
                                    <Component />
                                </NavbarProvider>
                            </AuthGuard>
                        }
                    />
                ),
            )}
        </Routes>
    )
}

export default RoutesConfig
