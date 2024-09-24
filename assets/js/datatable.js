import { DataTable, language, moment } from '../app';

export function generateDataTable(tableData, columnsKeys, selector = '#table') {
    tableData = tableData.map((jobTracking) => {
        const createdAtDate = new Date(jobTracking.createdAt.date);
        const formatedName = !!jobTracking.name ? jobTracking.name : 'Attente réponse candidature'
        // Créer un nouvel élément <a>
        const newLink = document.createElement('a');

        // Définir les attributs de l'élément <a>
        newLink.href = '/candidature/' + jobTracking.id;
        newLink.textContent = 'Visualiser';

        // Ajouter une classe à l'élément <a> (optionnel)
        // newLink.classList.add('btn', 'btn-primary');

        return {
            ...jobTracking,
            name: formatedName,
            createdAt: createdAtDate.toLocaleDateString(),
            link: newLink.outerHTML
        }
    })
    const columns = []

    columnsKeys.forEach((key)=>columns.push({data : key}))

    new DataTable(selector, {
        data: tableData,
        columns,
        language,
        responsive: true
        // config options...
    });
}