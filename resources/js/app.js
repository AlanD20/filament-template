import _ from 'lodash';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';
import Focus from '@alpinejs/focus';
import Persist from '@alpinejs/persist';
import Collapse from '@alpinejs/collapse';
import Intersect from '@alpinejs/intersect';
import Mousetrap from '@danharrin/alpine-mousetrap';
import Tooltip from '@ryangjchandler/alpine-tooltip';
import FormsAlpinePlugin from './forms/index';
// import FormsAlpinePlugin from '../../vendor/filament/forms/dist/module.esm';
import NotificationsAlpinePlugin from '../../vendor/filament/notifications/dist/module.esm';

// Lodash init
window._ = _;

Alpine.plugin(Intersect);
Alpine.plugin(Collapse);
Alpine.plugin(Focus);
Alpine.plugin(FormsAlpinePlugin);
Alpine.plugin(Mousetrap);
Alpine.plugin(NotificationsAlpinePlugin);
Alpine.plugin(Persist);
Alpine.plugin(Tooltip);

if (localStorage.getItem('collapsedGroups') == null) {
  localStorage.setItem('collapsedGroups', JSON.stringify([]));
}

Alpine.store('sidebar', {
  isOpen: Alpine.$persist(false).as('isOpen'),

  collapsedGroups: Alpine.$persist(null).as('collapsedGroups'),

  groupIsCollapsed: function (group) {
    return this.collapsedGroups?.includes(group);
  },

  collapseGroup: function (group) {
    if (this.collapsedGroups?.includes(group)) {
      return;
    }

    this.collapsedGroups = this.collapsedGroups.concat(group);
  },

  toggleCollapsedGroup: function (group) {
    this.collapsedGroups = this.collapsedGroups?.includes(group)
      ? this.collapsedGroups.filter(
          (collapsedGroup) => collapsedGroup !== group
        )
      : this.collapsedGroups.concat(group);
  },

  close: function () {
    this.isOpen = false;
  },

  open: function () {
    this.isOpen = true;
  },
});

Alpine.store(
  'theme',
  window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
);

window.addEventListener('dark-mode-toggled', (event) => {
  Alpine.store('theme', event.detail);
});

window
  .matchMedia('(prefers-color-scheme: dark)')
  .addEventListener('change', (event) => {
    Alpine.store('theme', event.matches ? 'dark' : 'light');
  });

Chart.defaults.font.family = `'DM Sans', sans-serif`;
Chart.defaults.color = '#6b7280';

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.start();

// window.Livewire.onPageExpired((response, message) => {
//   console.log('page expired :(', response, message);
// });

// window.Livewire.onError((resp, code) => {
//   console.log('catched error', resp, code);
//   return false;
// });
