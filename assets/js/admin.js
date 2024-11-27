const { default: tinymce } = require("tinymce");
const { generateDataTable } = require("./datatable");

document.addEventListener("DOMContentLoaded", function () {
    const tableDataSelector = document.querySelector(".js-table-data");
    const tableDatajobApi = JSON.parse(tableDataSelector.getAttribute("data-table-job-api"));
    const tableDataJobSource = JSON.parse(tableDataSelector.getAttribute("data-table-job-source"));
    const tableDataAction = JSON.parse(tableDataSelector.getAttribute("data-table-action"));
    const tableUsers = JSON.parse(tableDataSelector.getAttribute("data-table-users"));

    generateDataTable(
        tableDatajobApi,
        [
            "name",
            "description",
            "functionName",
            "url",
            "link"
        ],
        '#job-api-table',
        true,
        '/admin/job_service/#id');



    generateDataTable(
        tableDataJobSource,
        [
            "name",
            "job_count",
            "link"
        ],
        '#job-source-table',
        true,
        '/admin/job_source/#id');
    
    

        generateDataTable(
            tableDataAction,
            [
                "name",
                'setClosed',
                "jobTrackings_count",
                "link"
            ],
            '#action-table',
            true,
            '/admin/action/#id');



generateDataTable(tableUsers, ['email', 'roles'],            '#users-table',)

})