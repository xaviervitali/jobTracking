import { DataTable, language, moment } from '../app';

export function generateDataTable(tableData, columnsKeys = [
    "recruiter",
    "title",
    "name",
    "delai",
    "link"], createLink = true, totalCount, selector = '#table') {
    if (createLink) {
        tableData = tableData.map((jobTracking) => {

            const newLink = document.createElement('a');

            newLink.href = '/candidature/' + jobTracking.id;
            newLink.textContent = 'Visualiser';


            return {
                ...jobTracking,
                delai: getDelai(jobTracking),
                link: newLink.outerHTML
            }
        })
    }

    const columns = []

    columnsKeys.forEach((key) => columns.push({ data: key }))

    // DataTable.datetime('DD/MM/YYYY');

    new DataTable(selector, {
        data: tableData,
        columns,
        language,
        layout: {
            // topStart: true,
            // topEnd: null,
            bottomEnd: {

                paging: {
                    numbers: false
                }
            }
        },
        pageLength: 20,
        responsive: true,
        createdRow: function (row, data, dataIndex) {
            $(row).find('td').addClass('pb-3 align-content-center');
            if (!!data.set_closed) {
                $(row).find('td').addClass('color-grey');
            }
        },
        lengthChange: false,
        info: true,
        language: {
            "search":         "Rechercher:",
            paginate: {
                first: "Premier",
                last: "Dernier",
                next: "Suivant",
                previous: "Précédent"
            },
            info: `${tableData.length} résultats`,
        },
        responsive: true
    });
}

function getDelai(jobTracking) {
    const isFinalAction = jobTracking.set_closed
    let date = jobTracking.maxCreatedAt
    let startDate = isFinalAction ? moment(jobTracking.createdAt) : moment();

    const diffStr = startDate.diff(date, 'days')
    return diffStr


}