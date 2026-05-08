import Chart from 'chart.js/auto';

const rootStyles = getComputedStyle(document.documentElement);

Chart.defaults.font.family = rootStyles.getPropertyValue('--font-sans').trim() || 'Manrope, sans-serif';
Chart.defaults.color = rootStyles.getPropertyValue('--color-ink-950').trim() || '#eaf2ff';
Chart.defaults.borderColor = 'rgba(97, 145, 232, 0.28)';
Chart.defaults.plugins.legend.labels.usePointStyle = true;
Chart.defaults.plugins.legend.labels.boxWidth = 10;
Chart.defaults.plugins.legend.labels.boxHeight = 10;

window.Chart = Chart;
