import { noActionLabel } from "../app.js";
import "../styles/synthese.css";
import { generateColumnChart, generatePieChart } from './chart.js';
import { generateDataTable } from './datatable.js';
import { performSearch } from "./search.js";

document.addEventListener("DOMContentLoaded", function () {

  const tableDataSelector = document.querySelector(".js-table-data");
  const tableData = JSON.parse(tableDataSelector.getAttribute("data-table-items"));

  let labels = [...new Set(tableData.map(job => job.action_name))];
  const series = []
  labels.forEach(actionName => {
    series.push(tableData.filter(job => job.action_name === actionName).length)

  });



  const jobsCountPerDelay = JSON.parse(tableDataSelector.getAttribute("data-job-count-per-delay"));
  const categories = jobsCountPerDelay.map(e => e.delay_in_days + ' jours')
  const data =  jobsCountPerDelay.map(e => e.delay_count)
  generateColumnChart([{name:"Candidatures",data}], categories, 'Candidatures en cours', "#jobs-count-per-delay")

  generatePieChart(labels, series)

  document.getElementById('search-input').addEventListener('keyup', e => performSearch(e.target.value, tableData))

});

