import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';
import 'jquery';
// import 'bootstrap-table/dist/bootstrap-table.min.css';
// import 'bootstrap-table';

/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import "./styles/app.css";

// jQuery est nécessaire pour Bootstrap Table
import $ from 'jquery';
global.$ = global.jQuery = $;

// console.log("This log comes from assets/app.js - welcome to AssetMapper! 🎉");
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