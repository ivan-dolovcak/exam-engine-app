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

// Relative time formatting.

const cutoffs = new Map([
    [60, "second"],
    [60 * 60, "minute"],
    [60 * 60 * 24, "hour"],
    [60 * 60 * 24 * 7, "day"],
    [60 * 60 * 24 * 7 * 30, "week"],
    [60 * 60 * 24 * 7 * 30 * 365, "month"],
    [Infinity, "year"],
]);

const formatter = new Intl.RelativeTimeFormat(
    preferences.lang, { style: "short" });

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

    const diffSecs = Math.round((adjustedTimestamp.getTime() - Date.now()) / 1000);

    // Find the appropriate unit in cutoffs, depending on diff seconds:
    const unitIndex = [...cutoffs].findIndex(
        cutoff => cutoff[0] > Math.abs(diffSecs));

    const divisor = unitIndex ? [...cutoffs][unitIndex - 1][0] : 1;
    const unit = [...cutoffs][unitIndex][1];

    timestampEl.title = formatter.format(
        Math.floor(diffSecs / divisor), unit);
}
