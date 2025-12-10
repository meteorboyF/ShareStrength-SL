/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
      },
      colors: {
        'primary': {
          'DEFAULT': '#6D28D9',
          'light': '#8B5CF6',
          'dark': '#5B21B6',
        },
        'secondary': '#10B981',
        'neutral': {
          'light': '#F9FAFB',
          'DEFAULT': '#F3F4F6',
          'medium': '#9CA3AF',
          'dark': '#374151',
          'darkest': '#111827'
        }
      },
      animation: {
        'fade-in-up': 'fadeInUp 0.8s ease-out forwards',
      },
      keyframes: {
        fadeInUp: {
          '0%': { opacity: '0', transform: 'translateY(20px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        }
      }
    },
  },
  plugins: [],
}