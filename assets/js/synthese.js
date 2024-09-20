
import "../styles/synthese.css";
import ApexCharts from 'apexcharts'

document.addEventListener("DOMContentLoaded", function () {
  const chartDataSelector = document.querySelector(".js-chart-data");
  const categories = JSON.parse(chartDataSelector.getAttribute("data-chart-categories")).map(e => formatDate(e))
  const jobsData = JSON.parse(chartDataSelector.getAttribute("data-chart-jobs-data"));
  const responsesData = JSON.parse(chartDataSelector.getAttribute("data-chart-responses-data"));
    
  var options = {
    series: [
      {
        name: "Candidatures",
        data: jobsData,
      },
      {
        name: "Candidatures closes",
        data: responsesData,
      },
    ],
    chart: {
      height: 'auto',
      type: "radar",
    },
    yaxis: {
      stepSize: Math.max(...jobsData, ...responsesData) +1,
    },
    xaxis: {
      categories: categories,
    },
  };

  var chart = new ApexCharts(document.querySelector("#chart"), options);
  chart.render();
});

function formatDate(inputDate) {
    // Créer une nouvelle date à partir de la chaîne d'entrée (en ajoutant '-01' pour le jour)
    const date = new Date(inputDate + '-01');
    
    // Options pour formater le mois en français abrégé
    const options = { month: 'short', year: '2-digit' };

    // Formater la date en français
    const formattedDate = date.toLocaleDateString('fr-FR', options);

    // Retourner le résultat, avec la première lettre du mois en minuscule
  return formattedDate.charAt(0).toLowerCase() + formattedDate.slice(1);
  

}