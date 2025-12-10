import React, { useState } from 'react';
import { Link } from 'react-router-dom';

// --- MOCK DATA (Update image_url to match your files in public/products/) ---
const MOCK_PRODUCTS = [
  {
    id: 1,
    name: "Ergonomic Wheelchair",
    vendor: "MobilityPlus",
    price: 2499.99,
    category: "Mobility",
    stock_quantity: 10,
    image_url: "/products/wheelchair.png" // Change this filename!
  },
  {
    id: 2,
    name: "Braille Smartwatch",
    vendor: "TechVision",
    price: 299.50,
    category: "Vision",
    stock_quantity: 3, // Low stock test
    image_url: "/products/watch.jpg" // Change this filename!
  },
  {
    id: 3,
    name: "Hearing Aid Pro",
    vendor: "AudioLife",
    price: 899.00,
    category: "Hearing",
    stock_quantity: 15,
    image_url: "/products/hearing-aid.jpg" // Change this filename!
  },
  {
    id: 4,
    name: "Smart Walking Stick",
    vendor: "MobilityPlus",
    price: 120.00,
    category: "Mobility",
    stock_quantity: 8,
    image_url: "/products/stick.jpg" // Change this filename!
  },
  {
    id: 5,
    name: "Voice-To-Text Tablet",
    vendor: "TechVision",
    price: 450.00,
    category: "Communication",
    stock_quantity: 5,
    image_url: "/products/tablet.jpg" // Change this filename!
  }
];

// Extract unique categories for the filter buttons
const CATEGORIES = ['all', ...new Set(MOCK_PRODUCTS.map(p => p.category))];

const Marketplace = () => {
  const [filter, setFilter] = useState('all');

  // Filter Logic
  const filteredProducts = MOCK_PRODUCTS.filter(product => 
    filter === 'all' || product.category === filter
  );

  return (
    <div className="min-h-screen bg-neutral-light font-sans p-4 sm:p-6 lg:p-8">
      <div className="max-w-7xl mx-auto space-y-8">
        
        {/* Back Button */}
        <div>
            <Link to="/dashboard" className="inline-flex items-center gap-1.5 text-neutral-medium hover:text-neutral-dark font-semibold text-sm transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fillRule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clipRule="evenodd" />
                </svg>
                Back to Dashboard
            </Link>
        </div>

        {/* Header */}
        <header className="text-center animate-fade-in-up">
            <h1 className="text-4xl font-extrabold tracking-tight text-neutral-darkest sm:text-5xl">Assistive Technology Marketplace</h1>
            <p className="mt-4 max-w-2xl mx-auto text-lg text-neutral-medium">Discover tools and technology designed to support independence and daily living.</p>
        </header>

        {/* Filters */}
        <div className="flex flex-wrap justify-center gap-3 animate-fade-in-up" style={{animationDelay: '100ms'}}>
            {CATEGORIES.map((cat) => (
                <button
                    key={cat}
                    onClick={() => setFilter(cat)}
                    className={`px-4 py-2 rounded-full text-sm font-semibold transition shadow-sm border ${
                        filter === cat 
                        ? 'bg-primary text-white border-primary' 
                        : 'bg-white text-neutral-dark border-neutral-200 hover:bg-neutral-50'
                    }`}
                >
                    {cat.charAt(0).toUpperCase() + cat.slice(1)}
                </button>
            ))}
        </div>

        {/* Product Grid */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 animate-fade-in-up" style={{animationDelay: '200ms'}}>
            {filteredProducts.length > 0 ? (
                filteredProducts.map((product) => (
                    <div key={product.id} className="bg-white rounded-xl border border-neutral-200 shadow-sm flex flex-col overflow-hidden hover:-translate-y-2 hover:shadow-xl transition duration-300">
                        {/* Image Container */}
                        <div className="aspect-w-1 aspect-h-1 w-full h-48 overflow-hidden bg-neutral-100 relative">
                            <img 
                                src={product.image_url} 
                                alt={product.name} 
                                className="w-full h-full object-cover object-center"
                                onError={(e) => { e.target.src = 'https://placehold.co/400x400?text=No+Image'; }} // Fallback if image not found
                            />
                        </div>

                        {/* Content */}
                        <div className="p-4 flex flex-col flex-grow">
                            <div className="flex-grow">
                                <p className="text-xs font-semibold text-primary uppercase">{product.vendor}</p>
                                <h3 className="mt-1 font-bold text-lg text-neutral-darkest">{product.name}</h3>
                                <p className="mt-2 text-2xl font-extrabold text-neutral-darkest">${product.price.toFixed(2)}</p>
                                
                                {product.stock_quantity <= 5 && (
                                    <p className="text-xs font-bold text-red-600 mt-1">Only {product.stock_quantity} left in stock!</p>
                                )}
                            </div>
                            
                            <div className="mt-6">
                                <button className="w-full text-center font-bold text-sm bg-neutral-darkest text-white px-4 py-2 rounded-lg hover:bg-neutral-dark transition shadow-md">
                                    View Details
                                </button>
                            </div>
                        </div>
                    </div>
                ))
            ) : (
                <p className="col-span-full text-center text-neutral-medium py-10">No products found in this category.</p>
            )}
        </div>

      </div>
    </div>
  );
};

export default Marketplace;