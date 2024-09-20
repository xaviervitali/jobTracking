import '../styles/postit.css'




function deleteJob(id) {
  parent.window.location.href = "/candidature/" + id + "/delete";
}

function modifyJob() {
  document.querySelectorAll(".disableable").forEach((input) => {
    input.disabled = !input.disabled;
  });

  document.querySelectorAll(".action").forEach((div) => {
    div.classList.toggle("d-none");
  });
}

function handleJobModify() {
  const jobForm = document.querySelector("form[name=job_form]");
  jobForm.submit()
}

// app_note_delete
function deleteNote(id) {
  parent.window.location.href = "/note/" + id + "/delete";
}



// Écouter l'ouverture de la modal
const handleModifyModal = document.getElementById("handleModifyModal");
handleModifyModal.addEventListener("show.bs.modal", function (event) {
  // Le bouton qui déclenche la modal
  const button = event.relatedTarget;

  // Extraire les données du bouton via les attributs data-*
  const jobTrackingId = button.getAttribute("data-job-tracking-id");
  const jobTrackingAction = button.getAttribute("data-job-tracking-action");
  const jobTrackingCreatedAt = button.getAttribute("data-job-tracking-created-at");
  // Sélectionner les éléments du formulaire dans la modal et leur attribuer les valeurs
  const modal = this;
  modal.querySelector('form[name="job_tracking"]').setAttribute('action', '/action/' + jobTrackingId + '/edit')
  document.querySelector('input[name="job_tracking[action]"][value="' + jobTrackingAction + '"]').setAttribute('checked', true)

  modal.querySelector("#job_tracking_createdAt").value = jobTrackingCreatedAt;

});

window.modifyJob = modifyJob;
window.deleteJob = deleteJob;
window.handleJobModify = handleJobModify;
window.deleteNote = deleteNote;