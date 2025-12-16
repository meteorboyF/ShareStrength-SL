import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import { CartProvider } from './context/CartContext'; // <--- Import Provider

// Import all your pages
import LandingPage from './pages/LandingPage';
import Login from './pages/Login';
import UserDashboard from './pages/UserDashboard';
import PaymentHistory from './pages/PaymentHistory';
import PaymentInsights from './pages/PaymentInsights';
import Resources from './pages/Resources';
import Marketplace from './pages/MarketPlace'; // <--- Import this
import PostTask from './pages/PostTask'; // <--- Import this
import HelpMateDashboard from './pages/HelpMateDashboard'; // <--- Import this
import RegisterUser from './pages/RegisterUser';
import RegisterHelpMate from './pages/RegisterHelpMate';   // Updated filename
import ProductDetails from './pages/ProductDetails'; // <--- New
import Cart from './pages/Cart'; // <--- New
import Checkout from './pages/Checkout'; // <--- New
import ProfileView from './pages/ProfileView'; // <--- Import this
import TaskStatus from './pages/TaskStatus'; // <--- Import this
import ServicePayment from './pages/ServicePayment'; // <--- Import this
import TrustedContacts from './pages/TrustedContacts'; // <--- Import this
function App() {
  return (
    <CartProvider>
    <Router>
      <Routes>
        {/* Public Routes */}
        <Route path="/" element={<LandingPage />} />
        <Route path="/login" element={<Login />} />
        
        {/* User Routes */}
        <Route path="/dashboard" element={<UserDashboard />} />
        <Route path="/payment" element={<PaymentHistory />} />
        <Route path="/payment-insights" element={<PaymentInsights />} />
        <Route path="/resources" element={<Resources />} />
        <Route path="/marketplace" element={<Marketplace />} />
        <Route path="/marketplace/product/:id" element={<ProductDetails />} />
        <Route path="/cart" element={<Cart />} />
        <Route path="/checkout" element={<Checkout />} />
        <Route path="/post-task" element={<PostTask />} />
        <Route path="/helper-dashboard" element={<HelpMateDashboard />} />
        <Route path="/helpmate-dashboard" element={<HelpMateDashboard />} />
        <Route path="/register-helpmate" element={<RegisterHelpMate />} />
        <Route path="/register-user" element={<RegisterUser />} />
        <Route path="/profile/:type/:id" element={<ProfileView />} />
        <Route path="/task-status" element={<TaskStatus />} />
        <Route path="/service-payment" element={<ServicePayment />} />
        <Route path="/trusted-contacts" element={<TrustedContacts />} />
      </Routes>
    </Router>
    </CartProvider>
  );
}

export default App;