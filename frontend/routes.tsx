import { Routes, Route } from 'react-router'
import Home from 'Containers/Home'
import About from 'Containers/About'

const RoutesConfig = () => {
    return (
        <Routes>
            <Route path="/" element={<Home />} />
            <Route path="/about" element={<About />} />
        </Routes>
    )
}

export default RoutesConfig
