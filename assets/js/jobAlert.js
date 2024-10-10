import { performSearch } from "./search";

document.addEventListener("DOMContentLoaded", function () {
    const tableDataSelector = document.querySelector(".js-table-data");
    const tableDataAdzuna = JSON.parse(tableDataSelector.getAttribute("data-table-adzuna"));
    const tableDataFranceTelecom = JSON.parse(tableDataSelector.getAttribute("data-table-france-telecom"));
    
    document.getElementById("search-input-adzuna").addEventListener('keyup', e=>performSearch(e.target.value, tableDataAdzuna, 'job-list-adzuna', ['company', 'description']))
    document.getElementById("search-input-ft").addEventListener('keyup', e=>performSearch(e.target.value, tableDataFranceTelecom, 'job-list-ft', ['company', 'description']))
})
