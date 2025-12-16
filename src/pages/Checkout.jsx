import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useCart } from '../context/CartContext';

const Checkout = () => {
  const { cart, cartTotal, clearCart } = useCart();
  const navigate = useNavigate();
  const [loading, setLoading] = useState(false);

  const handlePayment = (e) => {
    e.preventDefault();
    setLoading(true);

    // Simulate payment processing
    setTimeout(() => {
      setLoading(false);
      clearCart();
      alert("Payment Successful! Thank you for your order.");
      navigate('/dashboard');
    }, 2000);
  };

  if (cart.length === 0) {
    navigate('/marketplace'); // Redirect if empty
    return null;
  }

  return (
    <div className="min-h-screen bg-neutral-light p-4 sm:p-8 font-sans">
      <div className="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8">
        
        {/* Left: Order Details */}
        <div className="space-y-6">
          <h2 className="text-2xl font-bold text-neutral-darkest">Order Details</h2>
          <div className="bg-white p-6 rounded-xl shadow-sm border border-neutral-200">
            <ul className="divide-y divide-neutral-100">
              {cart.map((item) => (
                <li key={item.id} className="py-3 flex justify-between">
                  <div>
                    <span className="font-bold text-neutral-dark">{item.name}</span>
                    <span className="text-xs text-neutral-500 block">Qty: {item.quantity}</span>
                  </div>
                  <span className="font-semibold text-neutral-darkest">${(item.price * item.quantity).toFixed(2)}</span>
                </li>
              ))}
            </ul>
            <div className="border-t border-neutral-200 mt-4 pt-4 flex justify-between text-xl font-bold text-primary">
              <span>Total To Pay</span>
              <span>${cartTotal.toFixed(2)}</span>
            </div>
          </div>
        </div>

        {/* Right: Payment Form */}
        <div className="space-y-6">
          <h2 className="text-2xl font-bold text-neutral-darkest">Payment</h2>
          <div className="bg-white p-6 rounded-xl shadow-lg border border-neutral-200">
            <form onSubmit={handlePayment} className="space-y-4">
              <div>
                <label className="block text-sm font-semibold mb-1">Cardholder Name</label>
                <input type="text" required className="w-full p-3 border rounded-lg bg-gray-50" placeholder="John Doe" />
              </div>
              <div>
                <label className="block text-sm font-semibold mb-1">Card Number</label>
                <input type="text" required className="w-full p-3 border rounded-lg bg-gray-50" placeholder="0000 0000 0000 0000" />
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-semibold mb-1">Expiry</label>
                  <input type="text" required className="w-full p-3 border rounded-lg bg-gray-50" placeholder="MM/YY" />
                </div>
                <div>
                  <label className="block text-sm font-semibold mb-1">CVC</label>
                  <input type="text" required className="w-full p-3 border rounded-lg bg-gray-50" placeholder="123" />
                </div>
              </div>

              <button 
                type="submit" 
                disabled={loading}
                className={`w-full py-4 rounded-xl text-white font-bold shadow-md transition ${loading ? 'bg-gray-400' : 'bg-primary hover:bg-primary-dark'}`}
              >
                {loading ? 'Processing Payment...' : `Pay $${cartTotal.toFixed(2)}`}
              </button>
            </form>
          </div>
        </div>

      </div>
    </div>
  );
};

export default Checkout;