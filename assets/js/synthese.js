import "../styles/synthese.css";


import { ApexCharts, moment, DataTable, language } from '../app';

document.addEventListener("DOMContentLoaded", function () {
  // chart
  const chartDataSelector = document.querySelector(".js-chart-data");
  const jobsData = JSON.parse(chartDataSelector.getAttribute("data-chart-jobs-data"));

  const series = Object.values(jobsData)
  const labels = Object.keys(jobsData)

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

  const chart = new ApexCharts(document.querySelector("#chart"), options);
  chart.render();

  //table
  const tableDataSelector = document.querySelector(".js-table-data");
  const tableData = JSON.parse(tableDataSelector.getAttribute("data-table-items")).map((jobTracking) => {
    const createdAtDate = new Date(jobTracking.createdAt.date);
    const formatedName = !!jobTracking.name ? jobTracking.name : 'Attente réponse candidature'
    // Créer un nouvel élément <a>
    const newLink = document.createElement('a');

    // Définir les attributs de l'élément <a>
    newLink.href = '/candidature/' + jobTracking.id;
    newLink.textContent = 'Visualiser';

    // Ajouter une classe à l'élément <a> (optionnel)
    // newLink.classList.add('btn', 'btn-primary');

    return {
      ...jobTracking,
      name: formatedName,
      createdAt: createdAtDate.toLocaleDateString(),
      link: newLink.outerHTML
    }
  })



  new DataTable('#table', {
    data: tableData,
    columns: [
      { data: "createdAt" },
      { data: "recruiter" },
      { data: "title" },
      { data: "name" },
      { data: "link" },
    ],
    language,
    responsive: true
    // config options...
  });
});
