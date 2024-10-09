import { generateDataTable } from "./datatable";
import 'jquery-ui/ui/widgets/autocomplete';
import 'jquery-ui/themes/base/autocomplete.css';

document.addEventListener("DOMContentLoaded", function () {
    const tableDataSelector = document.querySelector(".js-table-data");
    const tableData = JSON.parse(tableDataSelector.getAttribute("data-table-items"));

    generateDataTable(tableData);

    $(function () {
        
        $("#job_search_settings_city_autocomplete").autocomplete({
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
                $('#job_search_settings_city').val(ui.item.id);
            }
        });
    });
})