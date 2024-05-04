const GET = new URL(location.toString()).searchParams;

async function fetchDocument()
{
    const URL = `/app/api/document.php?documentID=${GET.get("documentID")}`;
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
}

window.addEventListener("beforeunload", (e) => { e.preventDefault(); });

window.addEventListener("DOMContentLoaded", () => {
    generateDocument();
});
