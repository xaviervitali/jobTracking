import { DataTable, language, moment } from '../app';
/**
 * 
 * @param {array} tableData : Tableau des données 
 * @param {array} columnsKeys : clés des données
 * @param {boolean} createLink : génération d'un lien vers candidature
 * @param {string} selector : /!\ il s'agit d'un id bien le faire commencer par #
 * @param {string} path : lien vers la page.  Indiquez #id 
 * 
 */
export function generateDataTable(
    tableData,
    columnsKeys,
    selector,
    createLink,
    path) {

    
    tableData = tableData.map(item => {

        let newItem = {};
        
        for (const key in item) {
            newItem[key] = item[key] === null ? '' : nl2br(item[key]);
        }

        if (createLink) {
            const newLink = document.createElement('a');
            newLink.href = path + item.id;
            if (path.includes('#id')) {
                newLink.href = path.replace('#id', item.id)
            }

            newLink.textContent = 'Visualiser';

            newItem = {
                ...newItem,
                link: newLink.outerHTML
            }
        }

        return newItem;
    });


    const columns = []

    columnsKeys.forEach((key) => columns.push({ data: key }))


    new DataTable(selector, {
        data: tableData,
        columns,
        language,
        layout: {
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
            if ('set_closed' in data && data.set_closed === 'true') {

                $(row).find('td').addClass('color-grey');
            }
        },

        columnDefs: [
            {
                // targets: [3], // Indiquez les colonnes à tronquer
                render: function (data, type, row) {
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

function nl2br(str) {
    return str.toString().replace(/\n/g, '<br>');
}