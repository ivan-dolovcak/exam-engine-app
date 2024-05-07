import { documentID, baseAPIURL } from "./generator.js";

export function collectAnswersJSON()
{
    const questionElements
        = Array.from(document.getElementsByClassName("question-element"));

    const documentAnswers = {};

    for (const questionEl of questionElements) {
        const ID = questionEl.data.ID;

        let inputs, answers;
        switch (questionEl.data.type) {
        case "shortAnswer":
            // Convert empty strings to null:
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

export function collectQuestionsJSON()
{
    const questionElements
        = Array.from(document.getElementsByClassName("question-element"));

    const documentQuestions = [];

    for (const questionEl of questionElements)
        documentQuestions.push(questionEl.data);

    return JSON.stringify(documentQuestions);
}

export async function editDocumentQuestions()
{
    const URL = `${baseAPIURL}&request=editDocumentQuestions`;

    await fetch(URL, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: "[" + collectQuestionsJSON() + ", " + collectAnswersJSON() + "]"
    });

    window.onbeforeunload = () => {};
    location.href = `/views/document_details.phtml?documentID=${documentID}`;
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
