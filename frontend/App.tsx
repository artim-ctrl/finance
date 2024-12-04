import { BrowserRouter } from 'react-router'
import RoutesConfig from './routes'
import { UserProvider } from 'Components/UserProvider'

const App = () => {
    return (
        <BrowserRouter>
            <UserProvider>
                <RoutesConfig />
            </UserProvider>
        </BrowserRouter>
    )
}

export default App
