import { QuestionElement } from "./QuestionElement.js";
import { postSubmission } from "./document_json.js";
import { loadAnswers, gradeAnswers } from "./load_answers.js";

// Parsing the GET vars in URL:
const GET = new URL(location.toString()).searchParams;

export const documentID = GET.get("documentID");
export const documentGenMode = GET.get("genmode") ?? "view";
export const baseAPIURL = `/app/api/document.php?documentID=${documentID}`;

async function fetchDocument()
{
    const URL = `${baseAPIURL}&request=load`;
    const response = await fetch(URL);
    return response.json();
}

async function fetchSubmission()
{
    const submissionID = GET.get("submissionID");
    const URL = `${baseAPIURL}&submissionID=${submissionID}&request`;
    const response = await fetch(URL);
    return response.json();
}

async function generateDocument(questions)
{
    const documentArea = document.getElementById("document-area");

    // Generate question divs:
    for (const questionData of questions) {
        const questionEl = new QuestionElement(questionData);

        documentArea.appendChild(questionEl);
    }

    const submitBtn = document.getElementById("btn-document-submit");
    submitBtn.addEventListener("click", postSubmission);
}

// Alert for data loss on reload:
window.onbeforeunload = (e) => { e.preventDefault(); };
window.oncontextmenu = (e) => { e.preventDefault(); };

window.addEventListener("DOMContentLoaded", async() => {
    const documentObject = await fetchDocument();

    // Set title:
    document.getElementById("document-name").innerText = documentObject.name;

    let documentContent = [];
    const documentArea = document.getElementById("document-area");
    try {
        documentContent = JSON.parse(documentObject.documentJSON);
    }
    catch (e) {
        if (documentGenMode === "view") {
            // Display error message:
            documentArea.innerText = documentObject.documentJSON;

            return;
        }
    }

    await generateDocument(documentContent);

    const documentSolution = JSON.parse(documentObject.solutionJSON);
    if (documentGenMode === "edit") {
        loadAnswers(documentSolution);

        const script = document.body.appendChild(document.createElement("script"));
        script.type = "module";
        script.src = "/static/document_app/editing_mode.js";
    }
    else if (documentGenMode === "review") {
        // Remove submit button:
        const submitBtn = document.getElementById("btn-document-submit");
        submitBtn.remove();
        for (const input of documentArea.elements) {
            input.disabled = true;
        }

        const submissionObject = await fetchSubmission();
        const submission = JSON.parse(submissionObject.submissionJSON);
        loadAnswers(submission);

        if (documentObject.type === "exam")
            gradeAnswers(documentSolution);
    }
});
