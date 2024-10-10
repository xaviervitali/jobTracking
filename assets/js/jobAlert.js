import moment from "moment";
import { generateDataTable } from "./datatable";

document.addEventListener("DOMContentLoaded", function () {
    const tableDataSelector = document.querySelector(".js-table-data");
    const tableDataAdzuna = JSON.parse(tableDataSelector.getAttribute("data-table-adzuna"));
    const tableDataFranceTelecom = JSON.parse(tableDataSelector.getAttribute("data-table-france-telecom"));
    
    let adzuneData = [];
    let franceTelecomData = [];

    tableDataAdzuna.forEach(job => {

        const link = createLink(job.redirect_url)
   
        adzuneData.push({ company: job.company.display_name, location: job.location.display_name, description: job.description, title: job.title, link, created: moment(job.created).format('DD/MM/YY') })
    })

    tableDataFranceTelecom.forEach(job => {
        
        const link = createLink( job.contact?.urlPostulation ?? 'https://candidat.francetravail.fr/offres/recherche/detail/' + job.id)

        franceTelecomData.push({
            company: job.entreprise.nom ?? 'non renseigné',
            // company_description: job.entreprise.description ?? 'non renseignée',
            type_contrat: job.typeContratLibelle, location: formatFtLocation(job.lieuTravail.libelle), description: job.description, title: job.intitule, link, created: moment(job.dateCreation).format('DD/MM/YY')
        })
    })

    generateDataTable(adzuneData, [
        "company",
        'created',
        "title",
        "description",
        "location",
        "link"], false, '#table_adzuna');
    // debugger
    generateDataTable(franceTelecomData, [
        "company",
        // "company_description",
        'created',
        "title",
        "description",
        "type_contrat",
        "location",
        "link"], false, "#table_ft");
})

function formatFtLocation(location){
    return location.slice(4) + ' ('+location.slice(0, 2) +')'
}

function createLink(url) {
    if (!url) {
        return 'Allez sur le site';
    }
    const link = document.createElement('a');

    link.href = url;
    link.textContent = 'Visualiser';
    link.target = "_blank"

    return link
}