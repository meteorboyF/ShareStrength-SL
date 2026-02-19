import React, { useState } from 'react';
import { X, CreditCard, Lock } from 'lucide-react';

const PaymentModal = ({ isOpen, onClose, amount, isMonthly, onConfirm }) => {
    const [isLoading, setIsLoading] = useState(false);
    
    // Kept your original state structure
    const [cardDetails, setCardDetails] = useState({
        number: '',
        expiry: '',
        cvc: '',
        name: ''
    });

    if (!isOpen) return null;

    // --- THE FIX ---
    // Convert amount to a number and ensure it is positive.
    // If it's invalid (NaN), default to 0.
    const displayAmount = Math.abs(parseFloat(amount) || 0);

    const handleInputChange = (e) => {
        const { name, value } = e.target;
        setCardDetails(prev => ({
            ...prev,
            [name]: value
        }));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setIsLoading(true);

        // Simulate payment processing
        await new Promise(resolve => setTimeout(resolve, 1500));

        setIsLoading(false);
        onConfirm();
    };

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-neutral-900/50 backdrop-blur-sm animate-in fade-in duration-200">
            <div className="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-in zoom-in-95 duration-200">
                {/* Header */}
                <div className="px-6 py-4 border-b border-neutral-100 flex justify-between items-center bg-neutral-50">
                    <h3 className="text-lg font-bold text-neutral-800 flex items-center">
                        <CreditCard className="w-5 h-5 mr-2 text-primary" />
                        Secure Donation
                    </h3>
                    <button
                        onClick={onClose}
                        className="p-1 rounded-full hover:bg-neutral-200 transition-colors text-neutral-500"
                    >
                        <X className="w-5 h-5" />
                    </button>
                </div>

                {/* Body */}
                <div className="p-6">
                    <div className="mb-6 flex justify-between items-end border-b pb-4 border-dashed border-neutral-200">
                        <div>
                            <p className="text-sm text-neutral-500">Total Amount</p>
                            {/* Uses displayAmount to ensure no negative numbers show */}
                            <p className="text-3xl font-bold text-neutral-900">${displayAmount}</p>
                        </div>
                        <div className="px-3 py-1 bg-primary/10 text-primary rounded-full text-xs font-semibold">
                            {isMonthly ? 'Monthly' : 'One-time'}
                        </div>
                    </div>

                    <form onSubmit={handleSubmit} className="space-y-4">
                        <div>
                            <label className="block text-xs font-semibold text-neutral-500 uppercase tracking-wide mb-1">Card Number</label>
                            <input
                                type="text"
                                name="number"
                                value={cardDetails.number}
                                onChange={handleInputChange}
                                placeholder="0000 0000 0000 0000"
                                className="w-full px-4 py-3 rounded-lg border border-neutral-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                                required
                            />
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="block text-xs font-semibold text-neutral-500 uppercase tracking-wide mb-1">Expiry</label>
                                <input
                                    type="text"
                                    name="expiry"
                                    value={cardDetails.expiry}
                                    onChange={handleInputChange}
                                    placeholder="MM/YY"
                                    className="w-full px-4 py-3 rounded-lg border border-neutral-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                                    required
                                />
                            </div>
                            <div>
                                <label className="block text-xs font-semibold text-neutral-500 uppercase tracking-wide mb-1">CVC</label>
                                <input
                                    type="text"
                                    name="cvc"
                                    value={cardDetails.cvc}
                                    onChange={handleInputChange}
                                    placeholder="123"
                                    className="w-full px-4 py-3 rounded-lg border border-neutral-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                                    required
                                />
                            </div>
                        </div>

                        <div>
                            <label className="block text-xs font-semibold text-neutral-500 uppercase tracking-wide mb-1">Name on Card</label>
                            <input
                                type="text"
                                name="name"
                                value={cardDetails.name}
                                onChange={handleInputChange}
                                placeholder="John Doe"
                                className="w-full px-4 py-3 rounded-lg border border-neutral-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                                required
                            />
                        </div>

                        <button
                            type="submit"
                            disabled={isLoading}
                            className="w-full py-4 mt-2 bg-primary hover:bg-primary-dark text-white rounded-xl font-bold text-lg shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center disabled:opacity-70 disabled:cursor-not-allowed"
                        >
                            {isLoading ? (
                                <span className="flex items-center">
                                    <svg className="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                                        <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Processing...
                                </span>
                            ) : (
                                <span className="flex items-center">
                                    Pay ${displayAmount}
                                </span>
                            )}
                        </button>

                        <div className="text-center">
                            <p className="text-xs text-neutral-400 flex items-center justify-center">
                                <Lock className="w-3 h-3 mr-1" />
                                256-bit SSL Encrypted Payment
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
};

export default PaymentModal;