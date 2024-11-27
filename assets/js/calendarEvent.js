export function createIcsFile(event) {
    const { max_created_at, recruiter, jobUrl, action_name } = event;
    const day =  formatDate(max_created_at.date);
    const dlName = `${action_name} ${recruiter}`;

    const encodeIcsField = (field) => field.replace(/[\\,;]/g, '\\$&').replace(/\n/g, '\\n');

    const icsContent = `
                    BEGIN:VCALENDAR
                    VERSION:2.0
                    PRODID:-//Our Company//NONSGML v1.0//EN
                    CALSCALE:GREGORIAN
                    BEGIN:VEVENT
                    DTSTAMP:${new Date().toISOString().replace(/-|:|\.\d+/g, "")}
                    DTSTART:${day}
                    DTEND:${day}
                    SUMMARY:${encodeIcsField(dlName)}
                    URL:${encodeIcsField(jobUrl)}
                    END:VEVENT
                    END:VCALENDAR`
        .replace(/ {4}/g, '').replace(/\n/, "");

    const blob = new Blob([icsContent], { type: 'text/calendar' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `${dlName}.ics`;
    link.click();
    URL.revokeObjectURL(url);
}

export function createGoogleCalendarLink(event) {
    const { max_created_at, recruiter, jobUrl, action_name } = event;
    const dlName = `${action_name} ${recruiter}`;
    const day = formatDate(max_created_at.date);
    const googleCalendarLink = `https://www.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent(dlName)}&dates=${day}/${day}&details=${encodeURIComponent(jobUrl)}`;
    window.open(googleCalendarLink) 
}

function formatDate(date) {
    return (date.split(" ")[0] + 'T090000Z').replace(/-/g , "");
}