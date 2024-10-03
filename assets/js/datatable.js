import { DataTable, language, moment } from '../app';

export function generateDataTable(tableData, columnsKeys = [
    "recruiter",
    "title",
    "name",
    "delai",
    "link"], selector = '#table') {
    tableData = tableData.map((jobTracking) => {
        
        const newLink = document.createElement('a');

        newLink.href = '/candidature/' + jobTracking.id;
        newLink.textContent = 'Visualiser';


        return {
            ... jobTracking,
            delai : getDelai(jobTracking),
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

function getDelai(jobTracking) {
    const isFinalAction = jobTracking.set_closed
    let date = jobTracking.maxCreatedAt
    let startDate = isFinalAction?moment(jobTracking.createdAt):moment();
    
    const diffStr = startDate.diff(date, 'days')
    return  diffStr


}