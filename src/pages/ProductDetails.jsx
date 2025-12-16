import React from 'react';
import { useParams, Link, useNavigate } from 'react-router-dom';
import { PRODUCTS } from '../data/products';
import { useCart } from '../context/CartContext';

const ProductDetails = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const { addToCart } = useCart();
  
  // Find the product matching the ID in the URL
  const product = PRODUCTS.find(p => p.id === parseInt(id));

  if (!product) {
    return <div className="text-center py-20 text-xl">Product not found!</div>;
  }

  const handleAddToCart = () => {
    addToCart(product);
    alert(`${product.name} added to cart!`);
  };

  return (
    <div className="min-h-screen bg-neutral-light font-sans p-6 lg:p-12">
      <div className="max-w-6xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden">
        
        <div className="flex flex-col md:flex-row">
          {/* Image Section */}
          <div className="w-full md:w-1/2 h-96 md:h-auto bg-gray-100 flex items-center justify-center p-8">
            <img 
              src={product.image_url} 
              alt={product.name} 
              className="max-h-full max-w-full object-contain hover:scale-105 transition duration-500"
              onError={(e) => { e.target.src = 'https://placehold.co/600x600?text=No+Image'; }}
            />
          </div>

          {/* Details Section */}
          <div className="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
            <Link to="/marketplace" className="text-neutral-500 hover:text-primary mb-6 inline-flex items-center text-sm font-bold">
              &larr; Back to Marketplace
            </Link>
            
            <p className="text-sm font-bold text-primary uppercase tracking-wide mb-2">{product.category}</p>
            <h1 className="text-3xl md:text-4xl font-extrabold text-neutral-darkest mb-4">{product.name}</h1>
            <p className="text-sm text-neutral-500 mb-6">Sold by <span className="font-semibold text-neutral-dark">{product.vendor}</span></p>
            
            <div className="text-3xl font-bold text-neutral-darkest mb-6">
              ${product.price.toFixed(2)}
            </div>

            <p className="text-neutral-medium leading-relaxed mb-8">
              {product.description}
            </p>

            <div className="flex flex-col sm:flex-row gap-4">
              <button 
                onClick={handleAddToCart}
                className="flex-1 bg-primary text-white font-bold py-4 px-8 rounded-xl shadow-lg hover:bg-primary-dark hover:-translate-y-1 transform transition"
              >
                Add to Cart
              </button>
              <button 
                onClick={() => {
                    handleAddToCart();
                    navigate('/cart');
                }}
                className="flex-1 bg-secondary text-white font-bold py-4 px-8 rounded-xl shadow-lg hover:bg-green-700 hover:-translate-y-1 transform transition"
              >
                Buy Now
              </button>
            </div>

            <p className="mt-4 text-xs text-center sm:text-left text-neutral-400">
              {product.stock_quantity > 0 ? `In Stock: ${product.stock_quantity} units available` : 'Out of Stock'}
            </p>
          </div>
        </div>
      </div>
    </div>
  );
};

export default ProductDetails;