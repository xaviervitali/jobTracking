
import { generateColumnChart, generatePieChart, generateStackedColumnsChart } from './chart.js';
import moment from 'moment';

document.addEventListener("DOMContentLoaded", function () {

  const width = document.querySelector(".container-xxl").clientWidth
  const widthByChart = (width / 2.5 > 500 ? width / 2.5 : width*0.9).toString();

  const chartDataSelector = document.querySelector(".js-chart-data");

//jobsPerMonth & closedJobsPerMonth
  const jobsPerMonth = JSON.parse(chartDataSelector.getAttribute("data-jobs-per-month"));
  const closedJobsPerMonth = JSON.parse(chartDataSelector.getAttribute("data-closed-jobs-per-month"));


  const colChatCategories = Object.keys(jobsPerMonth).map(yearmonth => moment(yearmonth, 'YYYY-MM').format('MMM YY'));
  const colChartSeries = [{ name: 'Candidatures', data: Object.values(jobsPerMonth) }, { name: 'Candidatures cloturées', data: Object.values(closedJobsPerMonth) }];

// jobsSources
  const jobsSources = JSON.parse(chartDataSelector.getAttribute("data-job-sources"));
  const pieSourceChartLabels = jobsSources.map(job => job.name); 
  const pieSourceChartSeries = jobsSources.map(job => job.count); 


  // jobsActions
  const jobsActions = JSON.parse(chartDataSelector.getAttribute("data-job-actions"));
  const pieActionChartLabels = jobsActions.map(job => job.name); 
  const pieActionChartSeries = jobsActions.map(job => job.count); 


  //actionsBySourceCount
  const actionsBySourceCount = JSON.parse(chartDataSelector.getAttribute("data-actions-by-source"));

  const actionsBySourceCategories = [...new Set(actionsBySourceCount.map(action => action.name))];

  const sources = [...new Set(actionsBySourceCount.map(action => action.source))];
  const actionsBySourceSeries = sources.map(source => ({
      name: source,
      data: Array(actionsBySourceCategories.length).fill(0) // Remplir avec des zéros
  }));

  actionsBySourceCount.forEach(action => {
      const index = actionsBySourceCategories.indexOf(action.name); // Trouver l'index de l'action
      const sourceIndex = sources.indexOf(action.source); // Trouver l'index de la source
      if (index !== -1 && sourceIndex !== -1) {
          actionsBySourceSeries[sourceIndex].data[index] = action["count"]; // Assigner la valeur
      }
  });


  // currentWeekJobs
  const currentWeekJobs = JSON.parse(chartDataSelector.getAttribute("data-current-week"));
  const chartCurrentWeekJobsSeries = [{ data: Object.values(currentWeekJobs) } ]
  const chartCurrentWeekJobsCategories = Object.keys(currentWeekJobs).map(date => moment(date).format('ddd DD'))

  //closed job actions

  const jobsClosedActions = JSON.parse(chartDataSelector.getAttribute("data-closed-actions"));
  const pieClosedActionChartLabels = jobsClosedActions.map(job => job.name); 
  const pieClosedActionChartSeries = jobsClosedActions.map(job => job.count); 

  // charts
  generatePieChart(pieActionChartLabels, pieActionChartSeries, 'Actions sur candidatures', '#chart-pie-action', widthByChart)
  generatePieChart(pieSourceChartLabels, pieSourceChartSeries, 'Sources des candidatures', '#chart-pie-source', widthByChart)
  generatePieChart(pieClosedActionChartLabels, pieClosedActionChartSeries, 'Causes de clôture', '#chart-pie-closed-action', widthByChart)
  generateColumnChart(colChartSeries, colChatCategories, 'Candidatures sur l\'année', '#chart-bar-jobs-per-month', widthByChart)
  generateColumnChart(chartCurrentWeekJobsSeries, chartCurrentWeekJobsCategories, 'Candidatures de la semaine', '#chart-bar-jobs-week', widthByChart)
  generateStackedColumnsChart(actionsBySourceSeries, actionsBySourceCategories, 'Actions par sources', '#chart-stacked-actions-by-source', widthByChart);
  
});