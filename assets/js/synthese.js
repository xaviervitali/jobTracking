import { noActionLabel } from "../app.js";
import "../styles/synthese.css";
import { generatePieChart } from './chart.js';
import { generateDataTable } from './datatable.js';

document.addEventListener("DOMContentLoaded", function () {

  const tableDataSelector = document.querySelector(".js-table-data");
  const tableData = JSON.parse(tableDataSelector.getAttribute("data-table-items"));

  let labels = [...new Set(tableData.map(job => job.action_name))];
  const series = []
  labels.forEach(actionName => {
    series.push(tableData.filter(job => job.action_name === actionName).length)

  });


  generatePieChart(labels, series)

  document.getElementById('search-input').addEventListener('keyup', e => performSearch(e.target.value, tableData))
  // generateDataTable(tableData );

});

function performSearch(query, jobsInProgress) {
  const jobList = document.getElementById('job-list');
  jobList.innerHTML = ''; // Vider la liste actuelle

  const filteredJobs = jobsInProgress.filter(job =>
    job.recruiter.toLowerCase().includes(query.toLowerCase()) ||
    job.description.toLowerCase().includes(query.toLowerCase())
  );



  const jobCardTemplate = document.getElementById('cardTemplate').innerHTML;
  filteredJobs.forEach(job => {
    let jobElement = jobCardTemplate;
    for (const key in job) {
      if (job.hasOwnProperty(key)) {
        const regex = new RegExp(`job_${key}`, 'g');
        const value =  typeof job[key]  === 'string' ? job[key].slice(0, 300) : job[key]
        
        jobElement = jobElement.replace(regex, value);
      }
    }
    jobList.innerHTML += jobElement;
  });
}