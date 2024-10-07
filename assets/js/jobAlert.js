import moment from "moment";
import { generateDataTable } from "./datatable";

document.addEventListener("DOMContentLoaded", function () {
    const tableDataSelector = document.querySelector(".js-table-data");
    const tableData = JSON.parse(tableDataSelector.getAttribute("data-table-items"));
    const totalCount = JSON.parse(tableDataSelector.getAttribute("data-table-count"));
    let data = [];
    tableData.sort((a, b) => a.created > b.created ? -1 : 1);

    tableData.forEach(job => {
        const link = document.createElement('a');

        link.href = job.redirect_url;
        link.textContent = 'Visualiser';

        data.push({ company: job.company.display_name, location: job.location.display_name, description: job.description, title: job.title, link, created: moment(job.created).format('DD/MM/YY') })
    })

    
    generateDataTable(data, [
        "company",
        'created',
        "title",
        "description",
        "location",
        "link"], false);
})