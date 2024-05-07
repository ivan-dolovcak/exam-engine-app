import { QuestionElement } from "./QuestionElement.js";
import { collectQuestionsJSON, editDocumentQuestions, postSubmission } from "./document_json.js";

function areAllAnswersProvided()
{
    const documentArea = document.getElementById("document-area");

    const questionElements = documentArea.getElementsByClassName("question-element");

    if (! questionElements.length) {
        return false;
    }

    // Special check for checkboxes: is at least one checked for each question:
    for (const questionEl of questionElements) {
        const inputs = questionEl.getElementsByTagName("input");

        if (questionEl.data.type === "multiChoice") {
            let isOneChecked = false;
            for (const input of inputs) {
                if (input.checked) {
                    isOneChecked = true;
                    break;
                }
            }

            if (! isOneChecked)
                return false;
        }
        else {
            for (const input of inputs)
                input.required = true;
        }
    }

    if (! documentArea.checkValidity())
        return false;

    return true;
}

function createMoveBtn(questionID)
{
    const moveBtn = document.createElement("button");
    moveBtn.draggable = true;
    moveBtn.type = "button";
    moveBtn.style.cursor = "grab";
    moveBtn.innerHTML = "<i class='bi bi-arrows-move'></i>";
    moveBtn.classList.add("btn", "btn-move");
    moveBtn.addEventListener("dragstart", (event) => {
        event.dataTransfer.setData("text/plain", questionID);
    });

    return moveBtn;
}

function createNewQuestionBtn()
{
    const btnNewQuestion = document.createElement("button");
    btnNewQuestion.type = "button";
    btnNewQuestion.style.transition = ".2s";
    btnNewQuestion.innerHTML = "<i class='bi bi-plus-lg'></i>";
    btnNewQuestion.classList.add("btn", "dropdown", "btn-new-question");
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

        let targetBtn = event.target;
        if (targetBtn.classList.contains("bi")) // icon selected
            targetBtn = targetBtn.parentElement;

        if (targetBtn.previousElementSibling === questionEl
            || targetBtn.nextElementSibling === questionEl)
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

function createNewOptionBtn(questionID)
{
    const btnNewOption = document.createElement("button");
    btnNewOption.type = "button";
    btnNewOption.innerHTML = "<i class='bi bi-plus-lg'></i>";
    btnNewOption.classList.add("btn");
    btnNewOption.addEventListener("click", () => {
        const questionEl = document.getElementById(questionID);
        questionEl.createMultiInput("???");
        questionEl.data.offeredAnswers.push("???");
    });

    return btnNewOption;
}

function modifyMultiOption(checkbox)
{
    checkbox.contentEditable = true;
    checkbox.dataset.initial = checkbox.innerText;
    checkbox.style.cursor = "text";

    checkbox.addEventListener("click", (event) => event.preventDefault());

    // Editing:
    checkbox.addEventListener("blur", () => {
        const optionIndex
            = this.data.offeredAnswers.indexOf(checkbox.dataset.initial);

        this.data.offeredAnswers[optionIndex] = checkbox.innerText.trim();
        checkbox.dataset.initial = checkbox.innerText;
    });

    // Deleting buttons:
    const deleteBtn = document.createElement("button");
    deleteBtn.type = "button";
    deleteBtn.className = "btn";
    deleteBtn.innerHTML = "<i class='bi bi-x-lg'></i>";
    deleteBtn.addEventListener("click", () => {
        const optionIndex
            = this.data.offeredAnswers.indexOf(checkbox.innerText);

        this.data.offeredAnswers.splice(optionIndex, 1);
        deleteBtn.parentElement.remove();
    });
    checkbox.parentElement.appendChild(deleteBtn);
}

function modify()
{
    // Title updating:
    this.titleEl.contentEditable = true;
    this.titleEl.addEventListener("blur", (event) => {
        event.preventDefault();
        this.data.title = this.titleEl.innerText.trim();
    });

    // Add "new option" button:
    if (this.data.type === "multiChoice" || this.data.type === "singleChoice")
        this.appendChild(createNewOptionBtn(this.id));

    // Fill-in editing:
    if (this.data.type === "fillIn") {
        this.inputsEl.contentEditable = true;
        this.inputsEl.title = "right click to add box...";
        this.inputsEl.style.pointer = "add";

        this.inputsEl.addEventListener("contextmenu", (event) => {
            if (! this.inputsEl.isSameNode(event.target)) {
                event.target.remove();
                this.data.partialText = this.inputsEl.innerHTML.replaceAll(
                    /<input.*?>/g, "@@@"
                );
                this.inputsEl.innerHTML = null;
                this.updateFillIn();
                return;
            }

            let selection = window.getSelection();
            let range = selection.getRangeAt(0);
            range.deleteContents();
            range.insertNode(document.createTextNode(" @@@ "));

            this.data.partialText = this.inputsEl.innerHTML.replaceAll(
                /<input.*?>/g, "@@@"
            );
            this.inputsEl.innerHTML = null;
            this.updateFillIn();
        });
    }

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

    this.headerBtns.appendChild(createMoveBtn(this.id));

    // Add delete buttons:
    this.headerBtns.appendChild(createDeleteQuestionBtn(this.id));
}

QuestionElement.prototype.modify = modify;
QuestionElement.prototype.modifyMultiOption = modifyMultiOption;

(() => {
    window.oncontextmenu = () => {}; // Enable the context menu.

    const documentArea = document.getElementById("document-area");
    documentArea.style.userSelect = "initial"; // Enable selecting.
    documentArea.classList.add("editing-mode");

    const submitBtn = document.getElementById("btn-document-submit");
    submitBtn.removeEventListener("click", postSubmission);
    submitBtn.addEventListener("click", () => {
        if (areAllAnswersProvided())
            editDocumentQuestions();
    });

    const questionElements = documentArea.getElementsByClassName("question-element");
    for (const questionEl of questionElements) {
        questionEl.modify();
        // Content updating (checkboxes):
        for (const checkbox of questionEl.querySelectorAll("label > span:first-of-type")) {
            questionEl.modifyMultiOption(checkbox);
        }
    }

    if (! questionElements.length)
        documentArea.appendChild(createNewQuestionBtn());
})();
