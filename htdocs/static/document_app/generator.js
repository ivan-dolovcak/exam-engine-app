import { QuestionElement } from "./QuestionElement.js";
import { postSubmission } from "./document_json.js";

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

async function generateDocument()
{
    const documentObject = await fetchDocument();

    const documentArea = document.getElementById("document-area");

    let documentContent = null;
    try {
        documentContent = JSON.parse(documentObject.documentJSON);
    }
    catch (e) {
        // Display error message:
        documentArea.innerText = documentObject.documentJSON;
        return;
    }

    // Set title:
    document.getElementById("document-name").innerText = documentObject.name;

    // Generate question divs:
    for (const questionData of documentContent) {
        const questionEl = new QuestionElement(questionData);

        documentArea.appendChild(questionEl);
    }

    const submitBtn = document.getElementById("btn-document-submit");
    // documentArea.appendChild(submitBtn); // Move button to botton of parent div.
    switch (documentGenMode) {
        case "view":
            submitBtn.addEventListener("click", postSubmission);
    }
}

// Alert for data loss on reload:
window.onbeforeunload = (e) => { e.preventDefault(); };

window.addEventListener("DOMContentLoaded", async() => {
    await generateDocument();

    if (documentGenMode === "edit") {
        const script = document.body.appendChild(document.createElement("script"));
        script.type = "module";
        script.src = "/static/document_app/editing_mode.js";
    }
});
