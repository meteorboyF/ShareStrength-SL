import React from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useCart } from '../context/CartContext';

const Cart = () => {
  const { cart, removeFromCart, updateQuantity, cartTotal } = useCart();
  const navigate = useNavigate();

  if (cart.length === 0) {
    return (
      <div className="min-h-screen bg-neutral-light flex flex-col items-center justify-center p-4">
        <h2 className="text-2xl font-bold text-neutral-darkest mb-4">Your Cart is Empty</h2>
        <Link to="/marketplace" className="bg-primary text-white px-6 py-3 rounded-lg font-bold hover:bg-primary-dark transition">
          Browse Marketplace
        </Link>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-neutral-light p-4 sm:p-8 font-sans">
      <div className="max-w-6xl mx-auto">
        <h1 className="text-3xl font-extrabold text-neutral-darkest mb-8">Shopping Cart</h1>

        <div className="flex flex-col lg:flex-row gap-8">
          {/* Cart Items */}
          <div className="lg:w-2/3 space-y-4">
            {cart.map((item) => (
              <div key={item.id} className="bg-white p-4 rounded-xl shadow-sm border border-neutral-200 flex items-center gap-4">
                <img 
                  src={item.image_url} 
                  alt={item.name} 
                  className="w-20 h-20 object-cover rounded-lg bg-gray-50"
                  onError={(e) => { e.target.src = 'https://placehold.co/100x100?text=Item'; }}
                />
                
                <div className="flex-grow">
                  <h3 className="font-bold text-neutral-darkest">{item.name}</h3>
                  <p className="text-sm text-neutral-medium">{item.vendor}</p>
                  <p className="text-primary font-bold mt-1">${item.price.toFixed(2)}</p>
                </div>

                <div className="flex items-center gap-3">
                  <button onClick={() => updateQuantity(item.id, -1)} className="w-8 h-8 rounded-full bg-neutral-100 flex items-center justify-center hover:bg-neutral-200 font-bold">-</button>
                  <span className="font-bold w-4 text-center">{item.quantity}</span>
                  <button onClick={() => updateQuantity(item.id, 1)} className="w-8 h-8 rounded-full bg-neutral-100 flex items-center justify-center hover:bg-neutral-200 font-bold">+</button>
                </div>

                <button onClick={() => removeFromCart(item.id)} className="text-red-500 hover:text-red-700 p-2">
                  <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </button>
              </div>
            ))}
          </div>

          {/* Summary / Checkout */}
          <div className="lg:w-1/3">
            <div className="bg-white p-6 rounded-xl shadow-lg border border-neutral-200 sticky top-8">
              <h2 className="text-xl font-bold mb-4 border-b border-neutral-100 pb-4">Order Summary</h2>
              
              <div className="flex justify-between mb-2 text-neutral-dark">
                <span>Subtotal</span>
                <span>${cartTotal.toFixed(2)}</span>
              </div>
              <div className="flex justify-between mb-4 text-neutral-dark">
                <span>Shipping</span>
                <span>Free</span>
              </div>
              
              <div className="flex justify-between mb-6 text-xl font-extrabold text-neutral-darkest border-t border-neutral-100 pt-4">
                <span>Total</span>
                <span>${cartTotal.toFixed(2)}</span>
              </div>

              <button 
                onClick={() => navigate('/checkout')}
                className="w-full bg-green-600 text-white font-bold py-3 rounded-lg hover:bg-green-700 transition shadow-md"
              >
                Proceed to Checkout
              </button>
              
              <Link to="/marketplace" className="block text-center mt-4 text-sm text-neutral-500 hover:text-primary">
                Continue Shopping
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Cart;