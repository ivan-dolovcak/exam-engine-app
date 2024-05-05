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

function createNewQuestionBtn()
{
    const btnNewQuestion = document.createElement("button");
    btnNewQuestion.type = "button";
    btnNewQuestion.innerHTML = "<i class='bi bi-plus-lg'></i>";
    btnNewQuestion.classList.add("btn", "dropdown");
    const template = document.getElementById("template-menu-new-question");
    btnNewQuestion.appendChild(template.content.cloneNode(true));

    for (const optionBtn of btnNewQuestion.getElementsByClassName("btn")) {
        optionBtn.addEventListener("click", () => {
            const questionType = optionBtn.dataset.type;

            const questionData = {
                ID: Math.random(),
                title: optionBtn.innerText,
                type: questionType,
            };

            if (questionType === "multiChoice" || questionType === "singleChoice") {
                questionData.offeredAnswers = ["a", "b", "c"];
            }
            else if (questionType === "fillIn")
                questionData.partialText = "Lorem @@@ ipsum @@@...";

            const newQuestion = new QuestionElement(questionData);

            const prevEl = btnNewQuestion.previousElementSibling;
                questionData.ordinal = 1;
            if (! prevEl || prevEl instanceof QuestionElement)
                btnNewQuestion.parentElement.insertBefore(
                    createNewQuestionBtn(), btnNewQuestion);

            const nextEl = btnNewQuestion.nextElementSibling;
            if (! nextEl || nextEl instanceof QuestionElement)
                btnNewQuestion.insertAdjacentElement(
                    "afterend", createNewQuestionBtn());

            // Calculate ordinal:
            questionData.ordinal = 0;
            if (prevEl)
                questionData.ordinal += prevEl.data.ordinal;
            if (nextEl)
                questionData.ordinal += nextEl.data.ordinal;

            if (! nextEl)
                questionData.ordinal = prevEl.data.ordinal + 1;
            else
                questionData.ordinal /= 2;

            btnNewQuestion.replaceWith(newQuestion);
        });
    }

    return btnNewQuestion;
}

(() => {
    const documentArea = document.getElementById("document-area");

    const inputs = documentArea.getElementsByTagName("input");

    // All answers must be provided:
    for (const input of inputs)
        if (input.type !== "checkbox")
            input.required = true;

    const submitBtn = document.getElementById("btn-document-submit");
    submitBtn.addEventListener("click", () => {});

    // Add "new question" buttons:
    const questionElements = documentArea.getElementsByClassName("question-element");

    for (const questionEl of questionElements) {
        questionEl.parentElement.insertBefore(createNewQuestionBtn(), questionEl);

        if (questionEl === questionElements[questionElements.length-1]) {
            questionEl.parentElement.appendChild(createNewQuestionBtn());
        }
    }
})();
