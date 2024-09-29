import { generateDataTable } from "./datatable";

document.addEventListener("DOMContentLoaded", function () {
    const tableDataSelector = document.querySelector(".js-table-data");
    const tableData = JSON.parse(tableDataSelector.getAttribute("data-table-items"));

    generateDataTable(tableData, [
        "createdAt",
        "recruiter",
        "title",
        "name",
        "link"
    ]);
})