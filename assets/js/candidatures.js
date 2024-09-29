
import { generateColumnChart, generatePieChart } from './chart.js';
import moment from 'moment';

document.addEventListener("DOMContentLoaded", function () {
  const chartDataSelector = document.querySelector(".js-chart-data");


  const jobsPerMonth = JSON.parse(chartDataSelector.getAttribute("data-jobs-per-month"));
  const closedJobsPerMonth = JSON.parse(chartDataSelector.getAttribute("data-closed-jobs-per-month"));

  const colChatCategories = Object.keys(jobsPerMonth).map(yearmonth => moment(yearmonth, 'YYYY-MM').format('MMM YY'));
  const colChartSeries = [{ name: 'Candidatures', data: Object.values(jobsPerMonth) }, { name: 'Candidatures cloturées', data: Object.values(closedJobsPerMonth) }];

  generateColumnChart(colChartSeries, colChatCategories, 'Candidatures sur l\'année', '#chart-bar-jobs-per-month')

  const jobsSources = JSON.parse(chartDataSelector.getAttribute("data-job-sources"));
  const pieSourceChartLabels = jobsSources.map(job => job.name); 
  const pieSourceChartSeries = jobsSources.map(job => job.count); 


  generatePieChart(pieSourceChartLabels, pieSourceChartSeries, 'Sources des candidatures', '#chart-pie-source ')
  
  const jobsActions = JSON.parse(chartDataSelector.getAttribute("data-job-actions"));
  const pieActionChartLabels = jobsActions.map(job => job.name); 
  const pieActionChartSeries = jobsActions.map(job => job.count); 

  let html = ''
  
  // jobsActions.forEach(action => {
  //   html += `<li class="list-group-item">${action.name} ${action.ratio * 100 } %</li>`
    
  // });
  // for (let i = 0; i < pieActionChartLabels.length; i++){
  //   html += `
  //   <li class="list-group-item">${pieActionChartLabels[i]} 
  //   ${Math.round(pieActionChartSeries[i] / totalJob * 100)} % </li>`
  // }
  
document.querySelector('.taux').innerHTML = html
  

  

  generatePieChart(pieActionChartLabels, pieActionChartSeries, 'Actions sur candidatures', '#chart-pie-action')


});