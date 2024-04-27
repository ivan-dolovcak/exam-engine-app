const GET = new URL(location.toString()).searchParams;

async function fetchDocument()
{
    const URL = `/app/api/document.php?documentID=${GET.get("documentID")}`;
    const response = await fetch(URL);
    return response.json();
}

async function generateDocument()
{
    const documentContent = await fetchDocument();
    const documentArea = document.getElementById("document-area");

    for (const questionData of documentContent) {
        const questionEl = new QuestionElement(questionData);

        documentArea.appendChild(questionEl);
    }
}

window.addEventListener("DOMContentLoaded", () => {
    generateDocument();
});
