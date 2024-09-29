import { DataTable, language, moment, noActionLabel } from '../app';

export function generateDataTable(tableData, columnsKeys, selector = '#table') {
    tableData = tableData.map((jobTracking) => {
        
        const date = jobTracking.createdAt
        const createdAtDate = moment(date).format('DD/MM/YYYY');
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
        responsive: true,
        createdRow: function (row, data, dataIndex) {
            
            if (!!data.set_closed) {
                $(row).find('td').addClass('color-grey');
            }
        }
        // config options...
    });
}

function formatActionField(jobTracking){
    let name = noActionLabel
    let date = jobTracking.createdAt
    

    if (jobTracking.name) {
        name = jobTracking.name
        date = jobTracking.maxCreatedAt
    }

    const diffStr = moment().diff(date, 'days')

    return name + ' (' + diffStr + 'j)'


}