import "../styles/synthese.css";
import { generateColumnChart, generatePieChart } from './chart.js';
import { performSearch } from "./search.js";

document.addEventListener("DOMContentLoaded", function () {

  const tableDataSelector = document.querySelector(".js-table-data");
  const tableData = JSON.parse(tableDataSelector.getAttribute("data-table-items"));

  let labels = [...new Set(tableData.map(job => job.action_name))];
  const series = []
  labels.forEach(actionName => {
    series.push(tableData.filter(job => job.action_name === actionName).length)

  });
  const width = document.querySelector("main").clientWidth
  const widthByChart = (width / 2.5 > 500 ? width / 2.5 : width*0.9).toString();


  const jobsCountPerDelay = JSON.parse(tableDataSelector.getAttribute("data-job-count-per-delay"));
  const categories = jobsCountPerDelay.map(e => e.delay_in_days + ' jours')
  const data =  jobsCountPerDelay.map(e => e.delay_count)
  generateColumnChart([{name:"Candidatures",data}], categories, 'Ancienneté des candidatures', "#jobs-count-per-delay", widthByChart)

  generatePieChart(labels, series,"Synthèse graphique","#chart",widthByChart)

  document.getElementById('search-input').addEventListener('keyup', e => performSearch(e.target.value, tableData,  'job-list', ['recruiter', 'title']))

});

