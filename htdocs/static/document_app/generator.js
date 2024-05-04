import { QuestionElement } from "./QuestionElement.js";
import { postSubmission } from "./document_json.js";

const GET = new URL(location.toString()).searchParams;
export const documentID = GET.get("documentID");
export const baseAPIURL = `/app/api/document.php?documentID=${documentID}`;

async function fetchDocument()
{
    const URL = `${baseAPIURL}&request=load`
    const response = await fetch(URL);
    return response.json();
}

async function generateDocument()
{
    const documentObject = await fetchDocument();

    const documentArea = document.getElementById("document-area");

    let documentContent = null;
    try {
        documentContent = JSON.parse(documentObject.documentJSON);
    }
    catch (e) {
        documentArea.innerText = documentObject.documentJSON;
        return;
    }

    document.getElementById("document-name").innerText = documentObject.name;

    for (const questionData of documentContent) {
        const questionEl = new QuestionElement(questionData);

        documentArea.appendChild(questionEl);
    }

    const generatingMode = GET.get("mode") ?? "view";

    const submitBtn = document.getElementById("btn-document-submit");
    documentArea.appendChild(submitBtn);
    switch (generatingMode) {
        case "view":
            submitBtn.addEventListener("click", postSubmission);
    }
}

window.onbeforeunload = (e) => { e.preventDefault(); };

window.addEventListener("DOMContentLoaded", async() => {
    await generateDocument();
});
