import { ApexCharts } from '../app';

/**
 * 
 * @param {*} labels : string[]
 * @param {*} series : number[]
 * @param {*} selector :string
 * 
 * @url https://apexcharts.com/javascript-chart-demos/pie-charts/simple-pie-chart/
 */
export function generatePieChart(labels, series, title = "Synthèse graphique", selector = "#chart", width = 500) {
console.log(width);

  const options = {
    title: { text: title },
    series,
    chart: {
      height:350,
      width,
      type: 'pie',
    },
    labels,
    dataLabels: {
      enabled: true,
      formatter: function (val, opts) {
        return opts.w.globals.series[opts.seriesIndex]
      },
      responsive: [{
        breakpoint: 500,
        options: {},
      }]

    }

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
export function generateColumnChart(series, categories, title = '', selector = "#chart", width = 500) {

  var options = {
    title: {
      text: title,

    },

    series,
    chart: {
      type: 'bar',
      height: 350,
      width
    },
    responsive: [{
      breakpoint: 500,
      options: {},
    }],
    dataLabels: {
      enabled: false
    },
    // stroke: {
    //   show: true,
    //   width: 2,
    //   colors: ['transparent']
    // },
    xaxis: {
      categories,
    },
    // yaxis: {
    //   title: {
    //     text: title
    //   }
    // },
    // fill: {
    //   opacity: 1
    // },

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
   responsive: [{
      breakpoint: 500,
      options: {},
    }],
    xaxis: {
      categories,
    },

  };

  var chart = new ApexCharts(document.querySelector(selector), options);
  chart.render();
}
