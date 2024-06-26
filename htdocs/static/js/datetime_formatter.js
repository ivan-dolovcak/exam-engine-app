window.addEventListener("DOMContentLoaded", () => {
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

    // For relative time formatting.
    const relativeCutoffs = new Map([
        [60, "second"],
        [60 * 60, "minute"],
        [60 * 60 * 24, "hour"],
        [60 * 60 * 24 * 7, "day"],
        [60 * 60 * 24 * 7 * 30, "week"],
        [60 * 60 * 24 * 7 * 30 * 365, "month"],
        [Infinity, "year"],
    ]);
    const relativeTimeFormatter = new Intl.RelativeTimeFormat(
        preferences.lang, { style: "short" });

    // Localize & timezone-adjust all timestamps:
    for (const timestampEl of document.getElementsByClassName("timestamp")) {
        if (! timestampEl.innerText) {
            timestampEl.innerText = "N/A";
            continue;
        }

        // When passing an UTC timestamp to the Date ctor, it adjusts it to
        // the local timezone.
        const adjustedTimestamp = new Date(timestampEl.innerText + " UTC");

        // Select appropriate formatting options:
        let timestampFormatOptions;
        switch(timestampEl.dataset.format) {
            case "date": timestampFormatOptions = dateFormatOptions; break;
            case "time": timestampFormatOptions = timeFormatOptions; break;
            case "datetime":
            default: timestampFormatOptions = dateTimeFormatOptions;
        }
        timestampEl.innerText = adjustedTimestamp.toLocaleString(
            preferences.lang, timestampFormatOptions);

        const diffSecs = Math.round(
            (adjustedTimestamp.getTime() - Date.now()) / 1000);

        // Find the appropriate unit in cutoffs, depending on diff seconds:
        const unitIndex = [...relativeCutoffs].findIndex(
            cutoff => cutoff[0] > Math.abs(diffSecs));

        const divisor = unitIndex ? [...relativeCutoffs][unitIndex - 1][0] : 1;
        const unit = [...relativeCutoffs][unitIndex][1];

        timestampEl.title = relativeTimeFormatter.format(
            Math.floor(diffSecs / divisor), unit);
    }
});
