import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
import colors from 'tailwindcss/colors';
import defaultTheme from 'tailwindcss/defaultTheme';

export default {
  darkMode: 'class',
  content: [
    './app/Filament/Resources/**/*.php',
    './app/Http/Livewire/**/*.php',
    './resources/**/*.blade.php',
    './vendor/filament/**/*.blade.php',
    './vendor/savannabits/filament-flatpickr/**/*.blade.php',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Nunito', ...defaultTheme.fontFamily.sans],
      },
      colors: {
        danger: colors.rose,
        primary: colors.red,
        success: colors.green,
        warning: colors.yellow,
      },
    },
  },
  plugins: [forms, typography],
};
