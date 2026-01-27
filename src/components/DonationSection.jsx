import React, { useState } from 'react';
import { Heart, Coffee, Gift, Trophy, ArrowRight } from 'lucide-react';

import api from '../services/api';
import PaymentModal from './PaymentModal';

const DonationSection = () => {
    const [amount, setAmount] = useState(50);
    const [isMonthly, setIsMonthly] = useState(false);
    const [customAmount, setCustomAmount] = useState('');
    const [isPaymentModalOpen, setIsPaymentModalOpen] = useState(false);

    const predefinedAmounts = [10, 25, 50, 100, 250];

    const handleDonateClick = () => {
        const donationAmount = customAmount || amount;
        if (!donationAmount) return alert('Please enter an amount.');
        setIsPaymentModalOpen(true);
    };

    const handlePaymentConfirm = async () => {
        try {
            const donationAmount = customAmount || amount;

            await api.post('/donations', {
                amount: donationAmount,
                currency: 'USD',
                is_monthly: isMonthly,
                status: 'completed',
                payment_method: 'credit_card'
            });

            setIsPaymentModalOpen(false);
            alert(`Thank you for your generous ${isMonthly ? 'monthly' : 'one-time'} donation of $${donationAmount}!`);
        } catch (error) {
            console.error('Donation failed:', error);
            alert('Something went wrong with your donation. Please try again.');
        }
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
                <div className="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-primary/5 rounded-full blur-3xl"></div>
                <div className="absolute top-[40%] -right-[10%] w-[30%] h-[30%] bg-secondary/5 rounded-full blur-3xl"></div>
            </div>

            <div className="container mx-auto px-4 relative z-10">
                <div className="max-w-4xl mx-auto text-center mb-12">
                    <h2 className="text-4xl md:text-5xl font-bold text-neutral-dark mb-4">
                        Support Our Mission
                    </h2>
                    <p className="text-xl text-neutral-600 max-w-2xl mx-auto">
                        Your contribution directly empowers communities. Every dollar makes a transparent, verified impact.
                    </p>
                </div>

                <div className="flex flex-col lg:flex-row gap-8 items-stretch">
                    {/* Donation Form */}
                    <div className="flex-1 bg-white rounded-3xl shadow-xl p-8 border border-neutral-100 will-animate transform hover:scale-[1.01] transition-transform duration-300">
                        <div className="flex items-center justify-center space-x-4 mb-8">
                            <div className="relative p-1 bg-neutral-100 rounded-full flex">
                                <button
                                    onClick={() => setIsMonthly(false)}
                                    className={`px-6 py-2 rounded-full font-medium transition-all duration-300 ${!isMonthly ? 'bg-primary text-white shadow-md' : 'text-neutral-500 hover:text-neutral-800'
                                        }`}
                                >
                                    One-time
                                </button>
                                <button
                                    onClick={() => setIsMonthly(true)}
                                    className={`px-6 py-2 rounded-full font-medium transition-all duration-300 ${isMonthly ? 'bg-primary text-white shadow-md' : 'text-neutral-500 hover:text-neutral-800'
                                        }`}
                                >
                                    Monthly
                                </button>
                            </div>
                        </div>

                        <div className="grid grid-cols-3 sm:grid-cols-5 gap-3 mb-6">
                            {predefinedAmounts.map((preset) => (
                                <button
                                    key={preset}
                                    onClick={() => {
                                        setAmount(preset);
                                        setCustomAmount('');
                                    }}
                                    className={`py-3 px-2 rounded-xl border-2 font-semibold transition-all duration-200 ${amount === preset && !customAmount
                                        ? 'border-primary bg-primary/5 text-primary'
                                        : 'border-neutral-100 text-neutral-600 hover:border-primary/50'
                                        }`}
                                >
                                    ${preset}
                                </button>
                            ))}
                        </div>

                        <div className="mb-8">
                            <label className="block text-sm font-medium text-neutral-600 mb-2">Custom Amount</label>
                            <div className="relative">
                                <span className="absolute left-4 top-1/2 -translate-y-1/2 text-neutral-500 font-semibold">$</span>
                                <input
                                    type="number"
                                    placeholder="Enter amount"
                                    value={customAmount}
                                    onChange={(e) => {
                                        setCustomAmount(e.target.value);
                                        setAmount(0);
                                    }}
                                    className="w-full pl-8 pr-4 py-3 rounded-xl border border-neutral-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all font-semibold text-lg"
                                />
                            </div>
                        </div>

                        <button
                            onClick={handleDonateClick}
                            className="w-full py-4 bg-primary hover:bg-primary-dark text-white rounded-xl font-bold text-lg shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center group"
                        >
                            <Heart className={`w-6 h-6 mr-2 ${isMonthly ? 'animate-pulse' : ''}`} />
                            Donate {customAmount ? `$${customAmount}` : `$${amount}`} {isMonthly ? 'Monthly' : 'Today'}
                            <ArrowRight className="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" />
                        </button>
                        <p className="text-center text-xs text-neutral-400 mt-4 flex items-center justify-center">
                            <span className="w-2 h-2 bg-green-500 rounded-full mr-2"></span> Secure SSL Encryption
                        </p>
                    </div>

                    {/* Impact Preview */}
                    <div className="flex-1 flex flex-col justify-center space-y-6">
                        <div className="bg-white p-6 rounded-2xl shadow-lg border border-neutral-100 flex items-start space-x-4 will-animate delay-100">
                            <div className="p-3 bg-secondary/10 rounded-xl text-secondary">
                                <Coffee className="w-8 h-8" />
                            </div>
                            <div>
                                <h3 className="font-bold text-lg text-neutral-800">Calls Provided</h3>
                                <p className="text-neutral-600">Your donation helps cover essential communication costs for volunteers.</p>
                            </div>
                        </div>

                        <div className="bg-white p-6 rounded-2xl shadow-lg border border-neutral-100 flex items-start space-x-4 will-animate delay-200">
                            <div className="p-3 bg-accent/10 rounded-xl text-accent">
                                <Gift className="w-8 h-8" />
                            </div>
                            <div>
                                <h3 className="font-bold text-lg text-neutral-800">Direct Aid</h3>
                                <p className="text-neutral-600">Funds go directly to verified requests for food, medicine, and shelter.</p>
                            </div>
                        </div>

                        <div className="bg-white p-6 rounded-2xl shadow-lg border border-neutral-100 flex items-start space-x-4 will-animate delay-300">
                            <div className="p-3 bg-green-50 rounded-xl text-green-600">
                                <Trophy className="w-8 h-8" />
                            </div>
                            <div>
                                <h3 className="font-bold text-lg text-neutral-800">Community Impact</h3>
                                <p className="text-neutral-600">Join 1,200+ donors making a real difference in Sri Lanka.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
};

export default DonationSection;
