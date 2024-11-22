import { titlelize } from "./utils";

/**
 * 
 * @param {string} query 
 * @param {array} jobsArray
 * @param {string} selectorId 
 * @param {array} fields 
 * @param {string} templateId
 */
export function performSearch(query, jobsArray, selectorId = 'job-list', fields = ['recruiter', 'description'], templateId = 'cardTemplate') {
    const jobList = document.getElementById(selectorId);
    jobList.innerHTML = ''; // Vider la liste actuelle

    const filteredJobs = jobsArray.filter(job =>
        fields.some(field => (
            job[field].toLowerCase().includes(query.toLowerCase()))
        ));



    const jobCardTemplate = document.getElementById(templateId).innerHTML;
    filteredJobs.forEach(job => {
        let jobElement = jobCardTemplate;
        for (const key in job) {
            
            if (job.hasOwnProperty(key)) {
                const regex = new RegExp(`job_${key}`, 'gi');
                let value = typeof job[key] === 'string' ? job[key].slice(0, 300) : job[key]
                if (key === 'delai') {
                    value = formatDelay(value);
                }
           
                
                jobElement = jobElement.replaceAll(regex, titlelize(value.toString()));
            }
        }

        
        jobList.innerHTML += jobElement;
    });
}

function formatDelay(delay) {


    let sentence = "aujourd'hui";
    const absDelay = Math.abs(delay);
    let jourStr = "jour";

    if (absDelay >= 1) {
        jourStr += "s";
    }

    if (delay > 0) {
        sentence = `il y a ${absDelay} ${jourStr}`;
    }

    if (delay < 0) {
        sentence = `dans ${absDelay} ${jourStr}`;
    }

    return sentence;
}