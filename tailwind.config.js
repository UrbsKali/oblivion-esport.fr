/** @type {import('tailwindcss').Config} */
export default {
  content: ['./src/**/**/**/*.{html,js,svelte,ts}',
  ],
  theme: {
    extend: {
      colors: {
        primary: { "50": "#aa8cbe", "100": "#9c79b3", "200": "#8d65a8", "300": "#7f529d", "400": "#713f92", "500": "#663983", "600": "#5a3275", "700": "#4f2c66", "800": "#442658", "900": "#392049" }
      }
    }
  },
};