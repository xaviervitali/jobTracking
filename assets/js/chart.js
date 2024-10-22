import { ApexCharts } from '../app';
import 'core-js/features/array';

const fill = {
  opacity: 0.9,
  colors: [
    '#9DD1C6', 
    '#172633', 
    '#ED8061', 
    '#3498DB', 
    '#E74C3C', 
    '#9B59B6', 
    '#F1C40F', 
    '#2F414C', 
    '#66DA26', 
    '#546E7A', 
    '#8E44AD', 
    '#1ABC9C', 
    '#34495E', 
    '#C0392B', 
    '#7F8C8D'  
  ]
};

const responsive = [{
  breakpoint: 500,
}];

/**
 * 
 * @param {*} labels : string[]
 * @param {*} series : number[]
 * @param {*} selector :string
 * 
 * @url https://apexcharts.com/javascript-chart-demos/pie-charts/simple-pie-chart/
 */
export function generatePieChart(labels, series, title = "Synthèse graphique", selector = "#chart", width = 500) {
  const type = labels.length > 3 ? 'donut' : 'pie';
  const options = {
    title: { text: title },
    series,
    chart: {
      height: 350,
      width,
      type
    },
    labels,
    legend: {
      show: true},
    
    dataLabels: {
      enabled: true,
      formatter: function (val, opts) {
        return  opts.w.globals.series[opts.seriesIndex]
      },
      responsive

    },
    fill

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
export function generateColumnChart(series, categories, title = '', selector = "#chart", width = 500, formatter=(val=>val)) {

  var options = {
    title: {
      text: title,

    },
    fill,
    series,
    chart: {
      type: 'bar',
      height: 350,
      width
    },
    responsive,
    dataLabels: {
      enabled: false
    },
    tooltip: {
      y: {
        formatter
        }
      },

    xaxis: {
      categories,
    },


  };

  var chart = new ApexCharts(document.querySelector(selector), options);
  chart.render();
}
/**
  * @param {*} series  array [{
        name: 'Net Profit',
        data: [44, 55, 57, 56, 61, 58, 63, 60, 66]
      },..]
 * @param {*} categories xLabels
 * @param {*} selector string 
 * @url  https://apexcharts.com/javascript-chart-demos/column-charts/stacked/
 */
export function generateStackedColumnsChart(series, categories, title = '', selector, width = 500) {
  var options = {
    title: {
      text: title,

    },
    legend: {
      show: true
    },
    series,
    chart: {
      type: 'bar',
      height: 350,
      width,
      stacked: true,
      toolbar: {
        show: true
      },
    },
    responsive,
    fill,
    xaxis: {
      categories,
      labels: { show: true, rotate: -45, rotateAlways: true, }
    },

  };

  var chart = new ApexCharts(document.querySelector(selector), options);
  chart.render();
}
