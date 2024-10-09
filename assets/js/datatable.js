import { DataTable, language, moment } from '../app';
/**
 * 
 * @param {*} tableData : Tableau des données 
 * @param {*} columnsKeys : clés des données
 * @param {*} createLink : génération d'un lien vers candidature
 * @param {*} selector : /!\ il s'agit d'un id bien le faire commencer par #
 */
export function generateDataTable(tableData, columnsKeys = [
    "recruiter",
    "title",
    "name",
    "delai",
    "link"], createLink = true, selector = '#table') {

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
            
            $(row).find('td').addClass('align-content-center');
            if (!!data.set_closed) {
                $(row).find('td').addClass('color-grey');
            }
        },
        columnDefs: [
            {
                targets: [3], // Indiquez les colonnes à tronquer
                render: function(data, type, row) {
                    if (type === 'display' && data.length > 500) { // Ajustez cette valeur selon vos besoins
                        return data.substring(0, 500) + '...';
                    }
                    return data;
                }
            }
        ],
        lengthChange: false,
        info: true,
        language: {
            infoEmpty: 'Aucune donnée',
            emptyTable: 'Aucune donnée',
            zeroRecords: 'Aucune donnée',
            search: "Rechercher:",
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