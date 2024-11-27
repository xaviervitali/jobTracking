import { generateDataTable } from "./datatable";
import 'jquery-ui/ui/widgets/autocomplete';
import 'jquery-ui/themes/base/autocomplete.css';
import moment from "moment";
import { titlelize } from "./utils";

document.addEventListener("DOMContentLoaded", function () {
    const tableDataSelector = document.querySelector(".js-table-data");
    const tableData = JSON.parse(tableDataSelector.getAttribute("data-table-items"));

    generateDataTable(tableData,
        [
            "recruiter",
            "title",
            "action_name",
            "delai",
            "link"
        ],
        '#table',
        true,
        '/candidature/#id');

    const tableDataAddress = JSON.parse(tableDataSelector.getAttribute("data-address-book"))
        .map(contact => {
            const shareButton = document.createElement('button');
            shareButton.setAttribute('data-contact', JSON.stringify(contact));
            shareButton.classList.add('share-contact', 'btn', 'btn-sm');
            shareButton.innerHTML = `<i class="fa-solid fa-arrow-up-from-bracket"></i>`

            const note = contact.note?.length > 100 ? contact.note.slice(0, 100) + '...' : contact.note

            return {
                ...contact,
                lastName: contact.lastName.toUpperCase(),
                firstName: titlelize(contact.firstName),
                createdAt: moment(contact.createdAt).format('D/M/Y'),
                note,
                button: shareButton.outerHTML
            }
        });

    generateDataTable(tableDataAddress,
        [
            'lastName',
            'firstName',
            'email',
            'company',
            'note',
            'phone',
            'button',
            'link'
        ],
        '#address-book-table',
        true,
        '/address/book/#id/edit');

    document.querySelectorAll('.share-contact').forEach(button => button.addEventListener('click', function (event) {
        const contact =  JSON.parse(event.currentTarget.getAttribute("data-contact"))
        
        createVCard(contact)

    }))

    $(function () {

        $(function () {
            const cityInput = $("#job_search_settings_city_autocomplete");

            const cityHiddenInput = $("#job_search_settings_city");

            // Initialiser le champ de saisie de la ville avec la valeur pré-enregistrée
            const preRegisteredCityId = cityHiddenInput.val();
            if (preRegisteredCityId) {
                // Faire une requête AJAX pour obtenir le nom de la ville à partir de l'ID
                $.ajax({
                    url: "/city/get-name/" + preRegisteredCityId,
                    success: function (data) {

                        cityInput.val(data.name);

                    }
                });
            }

            cityInput.autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: "/city/autocomplete",
                        data: { term: request.term },
                        success: function (data) {
                            response(data);
                        }
                    });
                },
                minLength: 2,
                select: function (event, ui) {
                    cityHiddenInput.val(ui.item.id);
                }
            });
        });
    });
})

function createVCard(contact) {
    const vcard = `BEGIN:VCARD
VERSION:4.0
FN:${contact.firstName} ${contact.lastName}
N:${contact.lastName};${contact.firstName}
ORG:${contact.company}
TEL:${contact.phone}
EMAIL:${contact.email}
NOTE:${contact.note}
END:VCARD`;

    const blob = new Blob([vcard], { type: 'text/vcard' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `${contact.firstName}_${contact.lastName}.vcf`;
    link.click();
    URL.revokeObjectURL(url);
}
