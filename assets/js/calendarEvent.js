export function createIcsFile(event) {
    const {  maxCreatedAt, recruiter, jobUrl, action_name } = event;
    debugger
    const day =  maxCreatedAt.split(" ").join('T').replace(/\W/g, '');
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
    const { title, start, end, description, location } = event;
    debugger
    const startTime = start.toISOString().replace(/-|:|\.\d+/g, "");
    const endTime = end.toISOString().replace(/-|:|\.\d+/g, "");
    const googleCalendarLink = `https://www.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent(title)}&dates=${startTime}/${endTime}&details=${encodeURIComponent(description)}&location=${encodeURIComponent(location)}`;
    return googleCalendarLink;
}