import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';

// Import all your pages
import LandingPage from './pages/LandingPage';
import Login from './pages/Login';
import UserDashboard from './pages/UserDashboard';
import PaymentHistory from './pages/PaymentHistory';
import PaymentInsights from './pages/PaymentInsights';
import Resources from './pages/Resources';
import Marketplace from './pages/MarketPlace'; // <--- Import this
import PostTask from './pages/PostTask'; // <--- Import this

function App() {
  return (
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
        <Route path="/post-task" element={<PostTask />} />
      </Routes>
    </Router>
  );
}

export default App;