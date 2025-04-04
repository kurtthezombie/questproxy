/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}"
  ],
  theme: {
    extend: {},
  },
  plugins: [require("daisyui")], 
  daisyui: {
    themes: ["light"], // ✅ Force the light theme
    base: true, // ✅ Ensure DaisyUI base styles are enabled
  },
}