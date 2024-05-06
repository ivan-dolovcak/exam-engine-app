import { QuestionElement } from "./QuestionElement.js";

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

function createMoveBtn()
{
    const moveBtn = document.createElement("button");
    moveBtn.type = "button";
    moveBtn.style.cursor = "grab";
    moveBtn.innerHTML = "<i class='bi bi-arrows-move'></i>";
    moveBtn.classList.add("btn");

    return moveBtn;
}

function createNewQuestionBtn()
{
    const btnNewQuestion = document.createElement("button");
    btnNewQuestion.type = "button";
    btnNewQuestion.style.transition = ".2s";
    btnNewQuestion.innerHTML = "<i class='bi bi-plus-lg'></i>";
    btnNewQuestion.classList.add("btn", "dropdown");
    const template = document.getElementById("template-menu-new-question");
    btnNewQuestion.appendChild(template.content.cloneNode(true));

    // Change icon on dragover:
    btnNewQuestion.addEventListener("dragover", (event) => {
        event.preventDefault();
        btnNewQuestion.classList.add("expand");
    });
    btnNewQuestion.addEventListener("dragleave", (event) => {
        btnNewQuestion.classList.remove("expand");
    });

    // Dragging logic:
    btnNewQuestion.addEventListener("drop", (event) => {
        event.preventDefault();
        btnNewQuestion.classList.remove("expand");

        const questionID = event.dataTransfer.getData("text/plain");
        const questionEl = document.getElementById(questionID);

        if (event.target === questionEl.previousElementSibling
            || event.target === questionEl.nextElementSibling)
        {
            return;
        }

        const questionElCopy = new QuestionElement(questionEl.data);
        questionEl.nextElementSibling.remove(); // Remove "new question" button.
        questionEl.remove();
        btnNewQuestion.replaceWith(questionElCopy);
    });

    for (const optionBtn of btnNewQuestion.getElementsByClassName("btn")) {
        optionBtn.addEventListener("click", () => {
            const questionType = optionBtn.dataset.type;

            // Define default question data:
            const questionData = {
                ID: Math.random(),
                title: optionBtn.innerText,
                type: questionType,
            };
            if (questionType === "multiChoice" || questionType === "singleChoice") {
                questionData.offeredAnswers = ["Lorem", "Ipsum", "Dolor"];
            }
            else if (questionType === "fillIn")
                questionData.partialText = "Lorem @@@ ipsum @@@ dolor...";

            const newQuestion = new QuestionElement(questionData);

            btnNewQuestion.replaceWith(newQuestion);
        });
    }

    return btnNewQuestion;
}

function createDeleteQuestionBtn(questionID)
{
    const btnDelete = document.createElement("button");
    btnDelete.type = "button";
    btnDelete.innerHTML = "<i class='bi bi-trash'></i>";
    btnDelete.classList.add("btn", "btn-delete-question");
    btnDelete.addEventListener("click", () => {
        const questionEl = document.getElementById(questionID);
        questionEl.nextElementSibling.remove(); // Remove "new question" button.
        questionEl.remove();
    });

    return btnDelete;
}


function modify()
{
    this.draggable = true;

    this.addEventListener("dragstart", (event) => {
        event.dataTransfer.setData("text/plain", event.target.id);
    });

    let prevEl = this.previousElementSibling;
    if (prevEl && ! (prevEl instanceof QuestionElement))
        prevEl = prevEl.previousElementSibling;
    let nextEl = this.nextElementSibling;
    if (nextEl && ! (nextEl instanceof QuestionElement))
        nextEl = nextEl.nextElementSibling;

    // Add "new question" buttons:
    if (! nextEl || this.nextElementSibling instanceof QuestionElement)
        this.insertAdjacentElement("afterend", createNewQuestionBtn());
    if (! prevEl || this.previousElementSibling instanceof QuestionElement)
        this.insertAdjacentElement("beforebegin", createNewQuestionBtn());

    this.headerBtns.appendChild(createMoveBtn());

    // Add delete buttons:
    this.headerBtns.appendChild(createDeleteQuestionBtn(this.id));
}

QuestionElement.prototype.modify = modify;

(() => {
    window.oncontextmenu = () => {}; // Enable the context menu.

    const documentArea = document.getElementById("document-area");
    documentArea.style.userSelect = "initial"; // Enable selecting.
    documentArea.classList.add("editing-mode");

    const inputs = documentArea.getElementsByTagName("input");

    // All answers must be provided:
    for (const input of inputs)
        if (input.type !== "checkbox")
            input.required = true;

    const submitBtn = document.getElementById("btn-document-submit");
    submitBtn.addEventListener("click", () => {});

    const questionElements = documentArea.getElementsByClassName("question-element");
    for (const questionEl of questionElements) {
        questionEl.modify();
    }
})();
