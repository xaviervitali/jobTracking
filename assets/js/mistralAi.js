let coverLetter = false

document.querySelector('#cover-letter').addEventListener('click', function (event) {
    event.preventDefault();
    if (!coverLetter) {

        const url = event.target.dataset.url;
        const jobDescription = document.querySelector("#job_form_offerDescription").value;
        const cv = document.querySelector('input[name="job_form[cv]"]:checked').value;
        const selector = document.querySelector('.cover-letter')

        fetch(url, {
            method: "POST",
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ jobDescription, cv })
        }).then(response =>
            response.json())
            .then(data => {
                coverLetter = true
                selector.classList.remove("text-center")
                selector.innerHTML = nl2br(data.coverLetter)
            })
            .catch(error => {
                selector.classList.remove("text-center")
                selector.classList.add('text-danger')
                selector.innerHTML = nl2br(error)
            });
    }
}
);

document.querySelector('#job_form_offerDescription').addEventListener('keyup', function (e) {

    const selector = document.querySelector('#cover-letter');
    const value = e.target.value.trim();
    value.length && value.length > 100 ? selector.removeAttribute('disabled') : selector.setAttribute('disabled', 'disabled')

})
function nl2br(str, is_xhtml) {
    if (typeof str === 'undefined' || str === null) {
        return '';
    }
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}