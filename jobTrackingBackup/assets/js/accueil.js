const moisAbreges = ['JANVIER','FÉVRIER', 'MARS', 'AVRIL', 'MAI', 'JUIN', 'JUILLET', 'AOÛT',' SEPTEMBRE','OCTOBRE', 'NOVEMBRE', 'DÉCEMBRE'];
const index = new Date().getMonth()+1

const chartXAxis = [...moisAbreges.slice(index), ... moisAbreges.slice(0, index)]

document.addEventListener('DOMContentLoaded', function () {
    const jobsElement = document.querySelector('.js-jobs');
    const jobsArray = JSON.parse(jobsElement.getAttribute('data-jobs'));

    const sended = new Array(12).fill(0);
    const closed = new Array(12).fill(0);

    jobsArray.forEach(element => {
        let currentIndex = new Date(element.createdAt).getMonth() -index
        if (currentIndex < 0) {
            currentIndex = 12 + currentIndex
        }
        sended[currentIndex] = sended[currentIndex] + 1
	
        if (!!element.closedAt) {
            closed[currentIndex] = closed[currentIndex] + 1
        }

    });


	
    var options = {
        series: [{
            name: 'Candidatures',
            data: sended
        },
        {
            name: 'Réponses',
            data: closed
        }],
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
            categories: chartXAxis,
        },
		    
        fill: {
            opacity: 1
        },
    };
		
    var chart = new ApexCharts(document.querySelector("#chart"), options);
    chart.render();
})