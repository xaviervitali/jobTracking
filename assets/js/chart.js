import { ApexCharts } from '../app';

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