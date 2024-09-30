
import { generateColumnChart, generatePieChart, generateStackedColumnsChart } from './chart.js';
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

  generatePieChart(pieActionChartLabels, pieActionChartSeries, 'Actions sur candidatures', '#chart-pie-action')

  const actionsBySourceCount = JSON.parse(chartDataSelector.getAttribute("data-actions-by-source"));

  const categories = actionsBySourceCount.map(action => action.name);
  const sources = [...new Set(actionsBySourceCount.map(action => action.source))];

  // Initialisation des séries
  const series = sources.map(source => ({
      name: source,
      data: Array(categories.length).fill(0) // Remplir avec des zéros
  }));

  // Remplissage des séries avec les données appropriées
  actionsBySourceCount.forEach(action => {
      const index = categories.indexOf(action.name); // Trouver l'index de l'action
      const sourceIndex = sources.indexOf(action.source); // Trouver l'index de la source
      if (index !== -1 && sourceIndex !== -1) {
          series[sourceIndex].data[index] = action["count"]; // Assigner la valeur
      }
  });
  generateStackedColumnsChart(series, categories, 'Actions par sources', '#chart-stacked-actions-by-source');

});