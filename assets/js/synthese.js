import { noActionLabel } from "../app.js";
import "../styles/synthese.css";
import { generatePieChart } from './chart.js';
import { generateDataTable } from './datatable.js';

document.addEventListener("DOMContentLoaded", function () {

  const tableDataSelector = document.querySelector(".js-table-data");
  const tableData = JSON.parse(tableDataSelector.getAttribute("data-table-items"));

  let labels = [...new Set(tableData.map(job => job.name))];
  const series = []
  labels.forEach(actionName => {
    series.push(tableData.filter(job=>job.name === actionName).length)
    
  });

  labels = labels.map(actionName => actionName ?? noActionLabel)
  
  generatePieChart(labels, series)


  generateDataTable(tableData );

});
