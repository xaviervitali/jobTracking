import "../styles/synthese.css";
import { createGoogleCalendarLink, createIcsFile } from "./calendarEvent.js";
import { generateColumnChart, generatePieChart } from './chart.js';
import { performSearch } from "./search.js";
import { plural } from "./utils.js";

document.addEventListener("DOMContentLoaded", function () {



  document.querySelectorAll(".btn.cal").forEach(btn => btn.addEventListener('click', function (event) {
    const button = event.target;

    const job = JSON.parse(button.getAttribute("data-job"))
    job.jobUrl = window.location.origin + button.getAttribute("data-job-url")

    switch (button.getAttribute("data-target")) {
      case "google":
        createGoogleCalendarLink(job)
        break;
      case "apple":
        createIcsFile(job)
        break;
      default:
        break;
    }
  }))

  const tableDataSelector = document.querySelector(".js-table-data");
  const tableData = JSON.parse(tableDataSelector.getAttribute("data-table-items"));

  let labels = [...new Set(tableData.map(job => job.name))];
  const series = []
  labels.forEach(actionName => {
    series.push(tableData.filter(job => job.name === actionName).length)

  });
  const width = document.querySelector("main").clientWidth
  const widthByChart = (width / 2.5 > 500 ? width / 2.5 : width * 0.9).toString();

  const delays = [... new Set(tableData.map(job => +job.delai))].sort((a, b) => a - b).reverse()

  const categories = []
  const data = []

  delays.forEach(delai => {
    const jobs = tableData.filter(job => job.delai === delai);
    data.push(jobs.length)
    categories.push(delai + plural(delai, ' jour', ' jours'))
  })


  

  
  generateColumnChart([{ name: "Candidatures", data }], categories, 'Ancienneté des candidatures', "#jobs-count-per-delay", widthByChart)

  generatePieChart(labels, series, "Synthèse graphique", "#chart", widthByChart)

  document.getElementById('search-input').addEventListener('keyup', e => {
    performSearch(e.target.value, tableData, 'job-list', ['recruiter', 'title'])

  })

});


