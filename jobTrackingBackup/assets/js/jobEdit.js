function deleteJob(id) {
  window.location = "/candidature/" + id + "/delete";
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
  const jobIdSelector = document.querySelector(".job-id");
  document.querySelector(".loading-spinner").classList.remove("d-none");
  const jobId = JSON.parse(jobIdSelector.getAttribute("data-id"));

  fetch("/candidature/" + jobId, {
    body: new FormData(jobForm),
    method: "POST",
  }).then((e) => {
    if (e.statusText === "OK") {
      location.reload();
    }
  });
}

c= document
  .querySelector("form[name=action]")
  .addEventListener("submit", (e) => {
     e.preventDefault();
        document.querySelector(".loading-spinner").classList.remove("d-none");
        const jobIdSelector = document.querySelector(".job-id");
        const action = document.querySelector("input[type='radio'][name='action[name]']:checked");

      const jobId = JSON.parse(jobIdSelector.getAttribute("data-id"));


        if (action) {

            const formData = new FormData();
            formData.append('jobId', jobId);
            formData.append('actionId', +action.value);
            fetch("/newJobTracking/" , {
                body:formData,
                method: "POST",
            }).then(response => response.json()
            ).then(e => {
                if (e === 'Ok') {
                  parent.window.location.href = '/'
            }})
        
        }
  });

