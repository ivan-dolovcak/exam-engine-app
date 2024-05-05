function areAllAnswersProvided()
{
    const documentArea = document.getElementById("document-area");

    if (! documentArea.checkVisibility())
        return false;

    const questionElements = documentArea.getElementsByClassName("question-element");

    // Special check for checkboxes: is at least one checked for each question:
    for (const questionEl of questionElements) {
        if (questionEl.data.type !== "multiChoice")
            continue;

        let isOneChecked = false;
        for (const input of questionEl.getElementsByTagName("input")) {
            if (input.checked) {
                isOneChecked = true;
                break;
            }
        }

        if (isOneChecked)
            return true;
    }

    return false;
}

(() => {
    const documentArea = document.getElementById("document-area");

    const inputs = documentArea.getElementsByTagName("input");

    for (const input of inputs)
        if (input.type !== "checkbox")
            input.required = true;

    const submitBtn = document.getElementById("btn-document-submit");
    submitBtn.addEventListener("click", () => {});
})();
