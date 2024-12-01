import { BrowserRouter } from 'react-router'
import Navbar from 'Components/Navbar'
import RoutesConfig from './routes'
import './App.css'

const App = () => {
    return (
        <BrowserRouter>
            <Navbar />
            <RoutesConfig />
        </BrowserRouter>
    )
}

export default App
