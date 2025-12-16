/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/views/**/*.twig",
        "./resources/views/**/*.html.twig",
        "./resources/js/**/*.js"
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    50: '#f0fdf4', // Greenish/Fresh for donations? Or stick to Blue? Let's go Premium Blue as planned.
                    // Improving the blue palette to be more "Stripe-like" or "Premium Tech"
                    50: '#f0f9ff',
                    100: '#e0f2fe',
                    200: '#bae6fd',
                    300: '#7dd3fc',
                    400: '#38bdf8',
                    500: '#0ea5e9',
                    600: '#0284c7',
                    700: '#0369a1', // Main brand color
                    800: '#075985',
                    900: '#0c4a6e',
                    950: '#082f49',
                },
                secondary: {
                    50: '#f8fafc',
                    100: '#f1f5f9',
                    200: '#e2e8f0',
                    300: '#cbd5e1',
                    400: '#94a3b8',
                    500: '#64748b',
                    600: '#475569',
                    700: '#334155',
                    800: '#1e293b',
                    900: '#0f172a', // Dark backgrounds
                    950: '#020617',
                },
                accent: {
                    50: '#fffbeb',
                    100: '#fef3c7',
                    200: '#fde68a',
                    300: '#fcd34d',
                    400: '#fbbf24', // Gold
                    500: '#f59e0b',
                    600: '#d97706',
                    700: '#b45309',
                    800: '#92400e',
                    900: '#78350f',
                }
            },
            fontFamily: {
                sans: ['Inter', 'system-ui', 'sans-serif'],
                heading: ['Outfit', 'Inter', 'system-ui', 'sans-serif'],
            }
        },
    },
    plugins: [],
}
