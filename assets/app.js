import 'bootstrap';
import ApexCharts from 'apexcharts';
import moment from 'moment';
import DataTable from 'datatables.net-dt';
import language from 'datatables.net-plugins/i18n/fr-FR.mjs';

import 'jquery';
import 'jquery-ui/ui/widgets/autocomplete';

// jQuery est nécessaire pour Bootstrap Table
import $ from 'jquery';
import 'jquery-ui/themes/base/autocomplete.css';
global.$ = global.jQuery = $;

moment.locale('fr-FR');
import 'bootstrap/dist/css/bootstrap.min.css';
import "./styles/app.css";

export { ApexCharts, moment, DataTable, language };
    
document.addEventListener('DOMContentLoaded', function() {
    const alertElement = document.getElementById('alert');
    const fadeOutBtn = document.getElementById('fadeOutBtn');
    const fadeInBtn = document.getElementById('fadeInBtn');

    if(fadeOutBtn &&fadeInBtn )
   { fadeOutBtn.addEventListener('click', function() {
        alertElement.classList.add('fade-out');
    });

    fadeInBtn.addEventListener('click', function() {
        alertElement.classList.remove('fade-out');
    });}
});

const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

export const noActionLabel = 'Attente réponse candidature'

