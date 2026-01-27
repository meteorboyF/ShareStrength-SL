export default {
    content: [
        "./index.html",
        "./src/**/*.{js,ts,jsx,tsx}",
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: '#6D28D9',
                    light: '#8B5CF6',
                    dark: '#5B21B6',
                },
                secondary: {
                    DEFAULT: '#10B981',
                    light: '#34D399',
                    dark: '#059669',
                },
                neutral: {
                    light: '#F3F4F6',
                    medium: '#9CA3AF',
                    dark: '#374151',
                    darkest: '#111827',
                }
            },
            fontFamily: {
                sans: ['Inter', 'sans-serif'],
            }
        },
    },
    plugins: [],
}
