import { useState, useEffect } from 'react';

const FONT_SIZES = [
  { key: 'small', label: 'A', title: 'Small' },
  { key: 'medium', label: 'A', title: 'Medium' },
  { key: 'large', label: 'A', title: 'Large' },
  { key: 'x-large', label: 'A', title: 'Extra Large' },
];

export default function AccessibilityWidget() {
  const [isOpen, setIsOpen] = useState(false);
  const [fontSize, setFontSize] = useState('medium');
  const [highContrast, setHighContrast] = useState(false);

  // Load settings from localStorage on mount
  useEffect(() => {
    const savedFontSize = localStorage.getItem('accessibility-font-size') || 'medium';
    const savedHighContrast = localStorage.getItem('accessibility-high-contrast') === 'true';

    setFontSize(savedFontSize);
    setHighContrast(savedHighContrast);

    applySettings(savedFontSize, savedHighContrast);
  }, []);

  // Apply settings to document
  const applySettings = (size, contrast) => {
    const root = document.documentElement;

    // Remove all font size classes
    FONT_SIZES.forEach(f => root.classList.remove(`font-${f.key}`));
    // Add current font size class
    root.classList.add(`font-${size}`);

    // Toggle high contrast
    if (contrast) {
      root.classList.add('high-contrast');
    } else {
      root.classList.remove('high-contrast');
    }
  };

  // Handle font size change
  const handleFontSizeChange = (size) => {
    setFontSize(size);
    localStorage.setItem('accessibility-font-size', size);
    applySettings(size, highContrast);
  };

  // Handle high contrast toggle
  const handleContrastToggle = () => {
    const newValue = !highContrast;
    setHighContrast(newValue);
    localStorage.setItem('accessibility-high-contrast', newValue.toString());
    applySettings(fontSize, newValue);
  };

  return (
    <div className="fixed bottom-4 right-4 z-50">
      {/* Settings Panel */}
      {isOpen && (
        <div
          className={`mb-2 p-4 rounded-lg shadow-lg border ${
            highContrast
              ? 'bg-black border-white'
              : 'bg-white border-gray-200'
          }`}
          style={{ minWidth: '200px' }}
        >
          {/* Font Size */}
          <div className="mb-4">
            <label className={`block text-sm font-medium mb-2 ${
              highContrast ? 'text-white' : 'text-gray-700'
            }`}>
              Text Size
            </label>
            <div className="flex gap-1">
              {FONT_SIZES.map((size, index) => (
                <button
                  key={size.key}
                  onClick={() => handleFontSizeChange(size.key)}
                  title={size.title}
                  className={`flex-1 py-2 px-2 rounded border transition-all ${
                    fontSize === size.key
                      ? highContrast
                        ? 'bg-white text-black border-white'
                        : 'bg-primary text-white border-primary'
                      : highContrast
                        ? 'bg-black text-white border-white hover:bg-gray-800'
                        : 'bg-gray-100 text-gray-700 border-gray-300 hover:bg-gray-200'
                  }`}
                  style={{ fontSize: `${12 + index * 3}px` }}
                >
                  {size.label}
                </button>
              ))}
            </div>
          </div>

          {/* High Contrast Toggle */}
          <div>
            <label className={`block text-sm font-medium mb-2 ${
              highContrast ? 'text-white' : 'text-gray-700'
            }`}>
              High Contrast
            </label>
            <button
              onClick={handleContrastToggle}
              className={`w-full py-2 px-4 rounded border transition-all flex items-center justify-between ${
                highContrast
                  ? 'bg-white text-black border-white'
                  : 'bg-gray-100 text-gray-700 border-gray-300 hover:bg-gray-200'
              }`}
            >
              <span>{highContrast ? 'On' : 'Off'}</span>
              <div className={`w-10 h-5 rounded-full relative transition-colors ${
                highContrast ? 'bg-green-500' : 'bg-gray-300'
              }`}>
                <div className={`absolute top-0.5 w-4 h-4 rounded-full bg-white shadow transition-transform ${
                  highContrast ? 'translate-x-5' : 'translate-x-0.5'
                }`} />
              </div>
            </button>
          </div>
        </div>
      )}

      {/* Toggle Button */}
      <button
        onClick={() => setIsOpen(!isOpen)}
        className={`w-12 h-12 rounded-full shadow-lg flex items-center justify-center transition-all hover:scale-110 ${
          highContrast
            ? 'bg-white text-black border-2 border-white'
            : 'bg-primary text-white'
        }`}
        title="Accessibility Settings"
        aria-label="Accessibility Settings"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          className="h-6 w-6"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            strokeLinecap="round"
            strokeLinejoin="round"
            strokeWidth={2}
            d="M12 4.5c-4.5 0-7.5 3-8.5 5.5 1 2.5 4 5.5 8.5 5.5s7.5-3 8.5-5.5c-1-2.5-4-5.5-8.5-5.5z"
          />
          <circle cx="12" cy="10" r="3" strokeWidth={2} />
          <path
            strokeLinecap="round"
            strokeWidth={2}
            d="M12 15v5M9 18h6"
          />
        </svg>
      </button>
    </div>
  );
}
