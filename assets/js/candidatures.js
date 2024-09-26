
import { generateDataTable } from './datatable.js';
import { generateColumnChart } from './chart.js';
import moment from 'moment';

document.addEventListener("DOMContentLoaded", function () {

  const tableDataSelector = document.querySelector(".js-table-data");
  const tableData = JSON.parse(tableDataSelector.getAttribute("data-table-items"));

  generateDataTable(tableData, [
    "createdAt",
    "recruiter",
    "title",
    "name",
    "link"
  ]);

  const jobsPerMonth = JSON.parse(tableDataSelector.getAttribute("data-jobs-per-month"));
  const closedJobsPerMonth = JSON.parse(tableDataSelector.getAttribute("data-closed-jobs-per-month"));

  const categories = Object.keys(jobsPerMonth).map(yearmonth => moment(yearmonth, 'YYYY-MM').format('MMM YY'));
  const series = [{ name: 'Candidatures', data: Object.values(jobsPerMonth) }, { name: 'Candidatures cloturées', data: Object.values(closedJobsPerMonth) }];

  generateColumnChart(series, categories, 'Candidatures sur l\'année')


});