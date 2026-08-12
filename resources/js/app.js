// Compass - Punto de entrada de JavaScript
//
// OJO con los nombres: './bootstrap' es el archivo de Laravel que configura
// axios, NO es el framework Bootstrap 5. El framework se importa abajo desde
// el paquete 'bootstrap' de npm.
import './bootstrap';

// Bootstrap 5: componentes JS (modales, dropdowns, tooltips, offcanvas...).
// Se expone en window para poder instanciarlos desde vistas Blade sueltas.
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

// Chart.js: graficos de los dashboards de BI (KPIs, tendencias, alertas EWS).
import Chart from 'chart.js/auto';
window.Chart = Chart;
