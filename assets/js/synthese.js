import "../styles/synthese.css";
import { generatePieChart } from './chart.js';
import { generateDataTable } from './datatable.js';

document.addEventListener("DOMContentLoaded", function () {

  // chart
  const chartDataSelector = document.querySelector(".js-chart-data");
  const jobsData = JSON.parse(chartDataSelector.getAttribute("data-chart-jobs-data"));

  const series = Object.values(jobsData);
  const labels = Object.keys(jobsData);

  generatePieChart(labels, series)
  //table
  const tableDataSelector = document.querySelector(".js-table-data");
  const tableData = JSON.parse(tableDataSelector.getAttribute("data-table-items"));


  generateDataTable(tableData, ["createdAt",
    "recruiter",
    "title",
    "name",
    "link"]);

});
