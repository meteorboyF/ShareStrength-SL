import React, { useState } from 'react';
import { useLocation, useNavigate, Link } from 'react-router-dom';

const ServicePayment = () => {
  const navigate = useNavigate();
  const [loading, setLoading] = useState(false);

  // In a real app, you'd pass data via ID. For this demo, we'll hardcode or use state.
  // Let's assume we are paying "Mike Ross" for "Garden Cleaning" from the dashboard mock.
  const PAYMENT_DETAILS = {
    id: 301,
    task_title: "Garden Cleaning",
    helpmate_name: "Mike Ross",
    helpmate_photo: "https://placehold.co/150",
    rate: 35.00,
    hours: 1.5, // 1 hour 30 mins
    platform_fee_percent: 0.10 // 10%
  };

  const subtotal = PAYMENT_DETAILS.rate * PAYMENT_DETAILS.hours;
  const platformFee = subtotal * PAYMENT_DETAILS.platform_fee_percent;
  const total = subtotal + platformFee;

  const handlePay = (e) => {
    e.preventDefault();
    setLoading(true);

    // Simulate API call
    setTimeout(() => {
      setLoading(false);
      alert(`Payment of $${total.toFixed(2)} to ${PAYMENT_DETAILS.helpmate_name} successful!`);
      navigate('/dashboard');
    }, 2000);
  };

  return (
    <div className="min-h-screen bg-neutral-light font-sans p-4 sm:p-8 flex items-center justify-center">
      <div className="max-w-2xl w-full bg-white rounded-2xl shadow-xl overflow-hidden">
        
        {/* Header */}
        <div className="bg-primary p-6 text-white text-center">
            <h1 className="text-2xl font-bold">Secure Service Payment</h1>
            <p className="text-indigo-200 text-sm mt-1">Completion of Task: #{PAYMENT_DETAILS.id}</p>
        </div>

        <div className="p-8">
            {/* Helpmate Info */}
            <div className="flex items-center gap-4 mb-8 pb-8 border-b border-neutral-100">
                <img src={PAYMENT_DETAILS.helpmate_photo} alt="Profile" className="w-16 h-16 rounded-full border-2 border-neutral-100" />
                <div>
                    <h2 className="text-lg font-bold text-neutral-darkest">Payment to {PAYMENT_DETAILS.helpmate_name}</h2>
                    <p className="text-neutral-medium text-sm">For {PAYMENT_DETAILS.task_title}</p>
                </div>
            </div>

            {/* Bill Breakdown */}
            <div className="bg-neutral-50 rounded-xl p-6 mb-8 border border-neutral-100">
                <div className="flex justify-between mb-3 text-sm">
                    <span className="text-neutral-600">Rate</span>
                    <span className="font-semibold">${PAYMENT_DETAILS.rate.toFixed(2)} /hr</span>
                </div>
                <div className="flex justify-between mb-3 text-sm">
                    <span className="text-neutral-600">Hours Logged</span>
                    <span className="font-semibold">{PAYMENT_DETAILS.hours} hrs</span>
                </div>
                <div className="flex justify-between mb-3 text-sm border-b border-neutral-200 pb-3">
                    <span className="text-neutral-600">Subtotal</span>
                    <span className="font-semibold">${subtotal.toFixed(2)}</span>
                </div>
                <div className="flex justify-between mb-2 text-sm">
                    <span className="text-neutral-600">Platform Fee (10%)</span>
                    <span className="font-semibold">${platformFee.toFixed(2)}</span>
                </div>
                <div className="flex justify-between mt-4 text-xl font-extrabold text-neutral-darkest">
                    <span>Total Due</span>
                    <span>${total.toFixed(2)}</span>
                </div>
            </div>

            {/* Payment Form (Simplified) */}
            <form onSubmit={handlePay}>
                <div className="mb-6">
                    <label className="block text-xs font-bold uppercase text-neutral-400 mb-2">Select Payment Method</label>
                    <div className="flex gap-3">
                        <button type="button" className="flex-1 py-3 border-2 border-primary bg-primary/5 text-primary font-bold rounded-lg text-sm">
                            Saved Card **** 4242
                        </button>
                        <button type="button" className="flex-1 py-3 border border-neutral-200 text-neutral-500 font-bold rounded-lg text-sm hover:bg-neutral-50">
                            New Card
                        </button>
                    </div>
                </div>

                <div className="flex gap-4">
                    <Link to="/dashboard" className="flex-1 py-4 text-center text-neutral-500 font-bold hover:text-neutral-800 transition">
                        Cancel
                    </Link>
                    <button 
                        type="submit" 
                        disabled={loading}
                        className={`flex-[2] py-4 rounded-xl text-white font-bold shadow-lg transition transform active:scale-95 ${loading ? 'bg-gray-400' : 'bg-green-600 hover:bg-green-700'}`}
                    >
                        {loading ? 'Processing...' : `Confirm & Pay $${total.toFixed(2)}`}
                    </button>
                </div>
            </form>
        </div>

      </div>
    </div>
  );
};

export default ServicePayment;