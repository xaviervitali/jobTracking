import { generateDataTable } from "./datatable";
import 'jquery-ui/ui/widgets/autocomplete';
import 'jquery-ui/themes/base/autocomplete.css';

document.addEventListener("DOMContentLoaded", function () {
    const tableDataSelector = document.querySelector(".js-table-data");
    const tableData = JSON.parse(tableDataSelector.getAttribute("data-table-items"));

    generateDataTable(tableData);

    $(function () {
        
        $(function () {
            const cityInput = $("#job_search_settings_city_autocomplete");
            
            const cityHiddenInput = $("#job_search_settings_city");
    
            // Initialiser le champ de saisie de la ville avec la valeur pré-enregistrée
            const preRegisteredCityId = cityHiddenInput.val();
            if (preRegisteredCityId) {
                // Faire une requête AJAX pour obtenir le nom de la ville à partir de l'ID
                $.ajax({
                    url: "/city/get-name/"+ preRegisteredCityId,
                    success: function (data) {
                        
                        cityInput.val(data.name);
                        
                    }
                });
            }
    
            cityInput.autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: "/city/autocomplete",
                        data: { term: request.term },
                        success: function(data) {
                            response(data);
                        }
                    });
                },
                minLength: 2,
                select: function(event, ui) {
                    cityHiddenInput.val(ui.item.id);
                }
            });
        });
    });
})