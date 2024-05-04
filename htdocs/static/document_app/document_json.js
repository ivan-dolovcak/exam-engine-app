import { documentID, baseAPIURL } from "./generator.js";

export function collectAnswersJSON()
{
    const questionElements
        = Array.from(document.getElementsByClassName("question-element"));

    const documentAnswers = {};

    for (const questionEl of questionElements) {
        const ID = questionEl.dataset.ID;

        let inputs, answers;
        switch (questionEl.dataset.type) {
        case "shortAnswer":
            if (! questionEl.input.value)
                documentAnswers[ID] = null;
            else
                documentAnswers[ID] = questionEl.input.value;
            break;
        case "singleChoice":
        case "multiChoice":
            inputs = Array.from(questionEl.inputsEl.getElementsByTagName("input"));
            answers = inputs.map(el => el.checked ? el.value : null);
            documentAnswers[ID] = answers;
            break;
        case "fillIn":
            inputs = Array.from(questionEl.inputsEl.children);
            answers = inputs.map(el => ! el.value ? null : el.value);
            documentAnswers[ID] = answers;
        }
    }

    return JSON.stringify(documentAnswers);
}

export async function postSubmission()
{
    const URL = `${baseAPIURL}&request=submission`;

    await fetch(URL, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: collectAnswersJSON(),
    });

    window.onbeforeunload = () => {};
    location.href = `/views/document_details.phtml?documentID=${documentID}`;
}
