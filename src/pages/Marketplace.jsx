import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import api from '../services/api';
import { useCart } from '../context/CartContext';

const Marketplace = () => {
  const [products, setProducts] = useState([]);
  const [filter, setFilter] = useState('all');
  const [loading, setLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState('');
  const { cartCount } = useCart();

  useEffect(() => {
    const fetchProducts = async () => {
      try {
        const response = await api.get('/products');
        setProducts(response.data);
      } catch (error) {
        console.error("Failed to fetch products", error);
      } finally {
        setLoading(false);
      }
    };
    fetchProducts();
  }, []);

  // Extract unique categories for the filter buttons
  const CATEGORIES = ['all', ...new Set(products.map(p => p.category))];

  // Filter Logic - now includes search
  const filteredProducts = products.filter(product => {
    const matchesCategory = filter === 'all' || product.category === filter;
    const matchesSearch = product.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
      product.description.toLowerCase().includes(searchQuery.toLowerCase());
    return matchesCategory && matchesSearch;
  });

  return (
    <div className="min-h-screen bg-neutral-light font-sans p-4 sm:p-6 lg:p-8">
      <div className="max-w-7xl mx-auto space-y-8">

        {/* Header with Back, Search, and Cart */}
        <div className="flex items-center justify-between gap-4">
          <Link to="/dashboard" className="inline-flex items-center gap-1.5 text-neutral-medium hover:text-neutral-dark font-semibold text-sm transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
              <path fillRule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clipRule="evenodd" />
            </svg>
            Back to Dashboard
          </Link>

          {/* Search Bar */}
          <div className="flex-1 max-w-md">
            <div className="relative">
              <input
                type="text"
                placeholder="Search products..."
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                className="w-full px-4 py-2 pl-10 border border-neutral-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
              />
              <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
          </div>

          {/* Cart Icon */}
          <Link to="/cart" className="relative inline-flex items-center gap-2 text-neutral-dark hover:text-primary font-semibold transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            {cartCount > 0 && (
              <span className="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                {cartCount}
              </span>
            )}
          </Link>
        </div>

        {/* Header */}
        <header className="text-center animate-fade-in-up">
          <h1 className="text-4xl font-extrabold tracking-tight text-neutral-darkest sm:text-5xl">Assistive Technology Marketplace</h1>
          <p className="mt-4 max-w-2xl mx-auto text-lg text-neutral-medium">Discover tools and technology designed to support independence and daily living.</p>
        </header>

        {/* Filters */}
        <div className="flex flex-wrap justify-center gap-3 animate-fade-in-up" style={{ animationDelay: '100ms' }}>
          {CATEGORIES.map((cat) => (
            <button
              key={cat}
              onClick={() => setFilter(cat)}
              className={`px-4 py-2 rounded-full text-sm font-semibold transition shadow-sm border ${filter === cat
                ? 'bg-primary text-white border-primary'
                : 'bg-white text-neutral-dark border-neutral-200 hover:bg-neutral-50'
                }`}
            >
              {cat.charAt(0).toUpperCase() + cat.slice(1)}
            </button>
          ))}
        </div>

        {/* Product Grid */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 animate-fade-in-up" style={{ animationDelay: '200ms' }}>
          {loading ? (
            <div className="col-span-full flex justify-center py-20">
              <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
            </div>
          ) : filteredProducts.length > 0 ? (
            filteredProducts.map((product) => (
              <div key={product.product_id} className="bg-white rounded-xl border border-neutral-200 shadow-sm flex flex-col overflow-hidden hover:-translate-y-2 hover:shadow-xl transition duration-300">
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
                    <p className="mt-2 text-2xl font-extrabold text-neutral-darkest">${parseFloat(product.price).toFixed(2)}</p>

                    {product.stock_quantity <= 5 && (
                      <p className="text-xs font-bold text-red-600 mt-1">Only {product.stock_quantity} left in stock!</p>
                    )}
                  </div>

                  <div className="mt-6 flex flex-col gap-3">
                    <Link
                      to={`/marketplace/product/${product.product_id}`}
                      className="w-full text-center font-semibold text-sm bg-neutral-darkest text-white px-4 py-2 rounded-lg hover:bg-neutral-dark transition shadow-md"
                    >
                      View Details
                    </Link>
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