function getCookie(cookieName)
{
    const cookies = document.cookie.split(";");
    for (const cookie of cookies)
        if (cookie.split("=")[0].trim() === cookieName)
            return decodeURIComponent(cookie.split("=")[1]);

    return null;
}

// Set theme:
const preferences = JSON.parse(getCookie("examEnginePreferences"));
const themeClassName = `${preferences.theme}-theme`;
document.documentElement.classList.add(themeClassName);

document.documentElement.lang = preferences.lang;

// Highlight current page anchor in nav:
const currentPage = location.pathname;
const a = document.querySelector(`nav a[href="${currentPage}"]`);
if (a)
    a.classList.add("active");

// Localize & timezone-adjust all timestamps:
const dateFormatOptions = {
    year: "numeric",
    month: "long",
    day: "numeric",
};
const timeFormatOptions = {
    hour: "numeric",
    minute: "numeric",
};
const dateTimeFormatOptions = {...dateFormatOptions, ...timeFormatOptions};

for (const timestampEl of document.getElementsByClassName("timestamp")) {
    if (! timestampEl.innerText) {
        timestampEl.innerText = "N/A";
        continue;
    }
    // When passing an UTC timestamp to the Date ctor, it adjusts it to
    // the local timezone.
    const adjustedTimestamp = new Date(timestampEl.innerText + " UTC");
    let timestampFormatOptions;
    switch(timestampEl.dataset.format) {
        case "date": timestampFormatOptions = dateFormatOptions; break;
        case "time": timestampFormatOptions = timeFormatOptions; break;
        case "datetime":
        default: timestampFormatOptions = dateTimeFormatOptions;
    }
    timestampEl.innerText = adjustedTimestamp.toLocaleString(
        preferences.lang, timestampFormatOptions);
}
