import { Outlet } from 'react-router-dom';
import Header from './components/Header.jsx';
import Footer from './components/Footer.jsx';

export default function App() {
  return (
    <div className="app-shell min-h-screen text-slate-950">
      <Header />
      <Outlet />
      <Footer />
    </div>
  );
}
