import { DataTable, language, moment } from '../app';

export function generateDataTable(tableData, columnsKeys, selector = '#table') {
    tableData = tableData.map((jobTracking) => {
        const createdAtDate = moment(jobTracking.createdAt.date).format('DD/MM/YYYY');
        const formatedName = formatActionField(jobTracking)
        const newLink = document.createElement('a');

        newLink.href = '/candidature/' + jobTracking.id;
        newLink.textContent = 'Visualiser';


        return {
            ...jobTracking,
            name: formatedName,
            createdAt: createdAtDate,
            link: newLink.outerHTML
        }
    })


    const columns = []

    columnsKeys.forEach((key) => columns.push({ data: key }))

    // DataTable.datetime('DD/MM/YYYY');

    new DataTable(selector, {
        data: tableData,
        columns,
        language,
        responsive: true
        // config options...
    });
}

function formatActionField(jobTracking){
    let name = 'Attente réponse candidature'
    let date = jobTracking.createdAt.date

    if (jobTracking.name) {
        name = jobTracking.name
        date = jobTracking.maxCreatedAt
    }

    const diffStr = moment().diff(date, 'days')

    return name + ' (' + diffStr + 'j)'


}