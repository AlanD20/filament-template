const colors = require("tailwindcss/colors");

module.exports = {
  darkMode: "class",
  content: [
    "./app/Filament/Resources/**/*.php",
    "./app/Http/Livewire/**/*.php",
    "./resources/**/*.blade.php",
    "./vendor/filament/**/*.blade.php",
    "./vendor/savannabits/filament-flatpickr/**/*.blade.php",
  ],
  theme: {
    extend: {
      fontFamily: {
        nrt: ["nrt", "Arial"],
      },
      colors: {
        danger: colors.rose,
        primary: colors.violet,
        success: colors.green,
        warning: colors.yellow,
      },
    },
  },
  plugins: [require("@tailwindcss/forms"), require("@tailwindcss/typography")],
};
