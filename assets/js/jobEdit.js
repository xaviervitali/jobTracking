const jobIdSelector = document.querySelector(".job-id");
const jobId = JSON.parse(jobIdSelector.getAttribute("data-id"));

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
// document.querySelector("form[name=action]").addEventListener("submit", (e) => {
//   e.preventDefault();
//   document.querySelector(".loading-spinner").classList.remove("d-none");

//   const action = document.querySelector(
//     "input[type='radio'][name='action[name]']:checked"
//   );

//   if (action) {
//     const formData = new FormData();
//     formData.append("jobId", jobId);
//     formData.append("actionId", +action.value);

//     fetch("/new_job_tracking/", {
//       body: formData,
//       method: "POST",
//     })
//       .then((response) => response.json())
//       .then((e) => {
//         if (e === "Ok") {
//           parent.window.location.reload();
//         }
//       });
//   }
// });

// app_note_delete
function deleteNote(id) {
  parent.window.location.href = "/note/" + id + "/delete";
}

document.querySelector("form[name=note]").addEventListener("submit", (e) => {
  e.preventDefault();
  const formData = new FormData(e.target);
  formData.set("note[job]", jobId);
  e.target.submit()
  // fetch("/note/new", {
  //   body: formData,
  //   method: "POST",
  // })
  //   .then((response) => response.json())
  //   .then((e) => {
  //     if (e === "Ok") {
  //       parent.window.location.reload();
  //     }
  //   });
});

// Écouter l'ouverture de la modal
var handleModifyModal = document.getElementById("handleModifyModal");
handleModifyModal.addEventListener("show.bs.modal", function (event) {
  // Le bouton qui déclenche la modal
  var button = event.relatedTarget;

  // Extraire les données du bouton via les attributs data-*
  var jobTrackingId = button.getAttribute("data-jobtracking-id");
  var jobTrackingAction = button.getAttribute("data-jobtracking-action");

  // Sélectionner les éléments du formulaire dans la modal et leur attribuer les valeurs
  var modal = this;
  modal.querySelector("#job_tracking_createdAt").value = jobTrackingId;
  modal.querySelector(
    "#job_tracking_action option[value = '" + jobTrackingAction + "']"
  ).setAttribute('selected', true)
  modal.querySelector('form[name="job_tracking"]').setAttribute('action', '/action/'+ jobTrackingId+'/edit')
});
