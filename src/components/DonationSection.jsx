import React, { useState } from 'react';
import { Heart, Coffee, Gift, Trophy, ArrowRight } from 'lucide-react';
import PaymentModal from './PaymentModal';
// Note: Removed api import if not used directly, or keep it if your backend is ready.
// import api from '../services/api'; 

const DonationSection = () => {
    const [amount, setAmount] = useState(50);
    const [isMonthly, setIsMonthly] = useState(false);
    const [customAmount, setCustomAmount] = useState('');
    const [isPaymentModalOpen, setIsPaymentModalOpen] = useState(false);

    const predefinedAmounts = [10, 25, 50, 100, 250];

    const handleDonateClick = () => {
        // 1. Get the final value (either custom or preset)
        let finalAmount = customAmount ? parseFloat(customAmount) : amount;

        // 2. Validation Checks
        if (!finalAmount || isNaN(finalAmount)) {
            return alert('Please enter a valid donation amount.');
        }

        if (finalAmount <= 0) {
            return alert('Donation amount must be greater than $0.');
        }

        // 3. Open Modal
        setIsPaymentModalOpen(true);
    };

    const handleCustomAmountChange = (e) => {
        const val = e.target.value;
        // Prevent negative signs completely
        if (val.includes('-')) return; 
        
        setCustomAmount(val);
        setAmount(0); // Reset preset selection
    };

    const handlePaymentConfirm = async () => {
        // This function is triggered by the Modal after "success"
        setIsPaymentModalOpen(false);
        const finalAmount = customAmount || amount;
        alert(`Thank you for your generous ${isMonthly ? 'monthly' : 'one-time'} donation of $${finalAmount}!`);
        
        // Reset form
        setCustomAmount('');
        setAmount(50);
    };

    return (
        <section className="py-20 bg-gradient-to-br from-neutral-50 to-neutral-100 relative overflow-hidden">
            <PaymentModal
                isOpen={isPaymentModalOpen}
                onClose={() => setIsPaymentModalOpen(false)}
                amount={customAmount || amount}
                isMonthly={isMonthly}
                onConfirm={handlePaymentConfirm}
            />

            {/* Background Decorative Elements */}
            <div className="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
                <div className="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-purple-100 rounded-full blur-3xl opacity-50"></div>
                <div className="absolute top-[40%] -right-[10%] w-[30%] h-[30%] bg-blue-100 rounded-full blur-3xl opacity-50"></div>
            </div>

            <div className="container mx-auto px-4 relative z-10">
                <div className="max-w-4xl mx-auto text-center mb-12">
                    <h2 className="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                        Support Our Mission
                    </h2>
                    <p className="text-xl text-gray-600 max-w-2xl mx-auto">
                        Your contribution directly empowers communities. Every dollar makes a transparent, verified impact.
                    </p>
                </div>

                <div className="flex flex-col lg:flex-row gap-8 items-stretch">
                    {/* Donation Form */}
                    <div className="flex-1 bg-white rounded-3xl shadow-xl p-8 border border-gray-100 transform hover:scale-[1.01] transition-transform duration-300">
                        
                        {/* Monthly vs One-time Toggle */}
                        <div className="flex items-center justify-center space-x-4 mb-8">
                            <div className="relative p-1 bg-gray-100 rounded-full flex">
                                <button
                                    onClick={() => setIsMonthly(false)}
                                    className={`px-6 py-2 rounded-full font-medium transition-all duration-300 ${!isMonthly ? 'bg-purple-600 text-white shadow-md' : 'text-gray-500 hover:text-gray-800'}`}
                                >
                                    One-time
                                </button>
                                <button
                                    onClick={() => setIsMonthly(true)}
                                    className={`px-6 py-2 rounded-full font-medium transition-all duration-300 ${isMonthly ? 'bg-purple-600 text-white shadow-md' : 'text-gray-500 hover:text-gray-800'}`}
                                >
                                    Monthly
                                </button>
                            </div>
                        </div>

                        {/* Preset Amounts */}
                        <div className="grid grid-cols-3 sm:grid-cols-5 gap-3 mb-6">
                            {predefinedAmounts.map((preset) => (
                                <button
                                    key={preset}
                                    onClick={() => {
                                        setAmount(preset);
                                        setCustomAmount('');
                                    }}
                                    className={`py-3 px-2 rounded-xl border-2 font-semibold transition-all duration-200 ${amount === preset && !customAmount
                                        ? 'border-purple-600 bg-purple-50 text-purple-700'
                                        : 'border-gray-200 text-gray-600 hover:border-purple-200'
                                        }`}
                                >
                                    ${preset}
                                </button>
                            ))}
                        </div>

                        {/* Custom Amount Input */}
                        <div className="mb-8">
                            <label className="block text-sm font-medium text-gray-600 mb-2">Custom Amount</label>
                            <div className="relative">
                                <span className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold">$</span>
                                <input
                                    type="number"
                                    min="1"
                                    placeholder="Enter amount"
                                    value={customAmount}
                                    onChange={handleCustomAmountChange}
                                    className="w-full pl-8 pr-4 py-3 rounded-xl border border-gray-200 focus:border-purple-600 focus:ring-2 focus:ring-purple-100 outline-none transition-all font-semibold text-lg"
                                />
                            </div>
                        </div>

                        {/* Donate Button */}
                        <button
                            onClick={handleDonateClick}
                            className="w-full py-4 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-bold text-lg shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center group"
                        >
                            <Heart className={`w-6 h-6 mr-2 ${isMonthly ? 'animate-pulse' : ''}`} />
                            Donate {customAmount ? `$${customAmount}` : `$${amount}`} {isMonthly ? 'Monthly' : 'Today'}
                            <ArrowRight className="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" />
                        </button>
                        <p className="text-center text-xs text-gray-400 mt-4 flex items-center justify-center">
                            <span className="w-2 h-2 bg-green-500 rounded-full mr-2"></span> Secure SSL Encryption
                        </p>
                    </div>

                    {/* Impact Preview (Right Side) */}
                    <div className="flex-1 flex flex-col justify-center space-y-6">
                        <div className="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 flex items-start space-x-4">
                            <div className="p-3 bg-blue-50 rounded-xl text-blue-600">
                                <Coffee className="w-8 h-8" />
                            </div>
                            <div>
                                <h3 className="font-bold text-lg text-gray-800">Calls Provided</h3>
                                <p className="text-gray-600">Your donation helps cover essential communication costs for volunteers.</p>
                            </div>
                        </div>

                        <div className="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 flex items-start space-x-4">
                            <div className="p-3 bg-pink-50 rounded-xl text-pink-600">
                                <Gift className="w-8 h-8" />
                            </div>
                            <div>
                                <h3 className="font-bold text-lg text-gray-800">Direct Aid</h3>
                                <p className="text-gray-600">Funds go directly to verified requests for food, medicine, and shelter.</p>
                            </div>
                        </div>

                        <div className="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 flex items-start space-x-4">
                            <div className="p-3 bg-green-50 rounded-xl text-green-600">
                                <Trophy className="w-8 h-8" />
                            </div>
                            <div>
                                <h3 className="font-bold text-lg text-gray-800">Community Impact</h3>
                                <p className="text-gray-600">Join 1,200+ donors making a real difference.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
};

export default DonationSection;