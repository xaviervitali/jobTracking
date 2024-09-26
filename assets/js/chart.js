import { ApexCharts } from '../app';

/**
 * 
 * @param {*} labels : string[]
 * @param {*} series : number[]
 * @param {*} selector :string
 * 
 * @url https://apexcharts.com/javascript-chart-demos/pie-charts/simple-pie-chart/
 */
export function generatePieChart(labels, series, selector = "#chart") {


  const options = {
    title: { text: "Synthèse graphique" },
    series,
    chart: {
      width: 500,
      type: 'pie',
    },
    labels,
    responsive: [{
      breakpoint: 480,
      options: {
        chart: {
          // width: 200
        },
        legend: {
          position: 'bottom'
        }
      }
    }]
  };

  const chart = new ApexCharts(document.querySelector(selector), options);
  chart.render();
}

/**
 * 
 * @param {*} series  array [{
        name: 'Net Profit',
        data: [44, 55, 57, 56, 61, 58, 63, 60, 66]
      },..]
 * @param {*} categories xLabels
 * @param {*} selector string 
 * 
 * @url https://apexcharts.com/javascript-chart-demos/column-charts/basic/
 */
export function generateColumnChart(series, categories, title = '', selector = "#chart") {

  var options = {
    series,
    chart: {
      type: 'bar',
      height: 350
    },
    plotOptions: {
      bar: {
        horizontal: false,
        columnWidth: '55%',
        endingShape: 'rounded'
      },
    },
    dataLabels: {
      enabled: false
    },
    stroke: {
      show: true,
      width: 2,
      colors: ['transparent']
    },
    xaxis: {
      categories,
    },
    yaxis: {
      title: {
        text: title
      }
    },
    fill: {
      opacity: 1
    },

  };

  var chart = new ApexCharts(document.querySelector(selector), options);
  chart.render();
}