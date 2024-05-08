export function loadAnswers(answers)
{
    const questionElements
        = Array.from(document.getElementsByClassName("question-element"));

    for (const questionEl of questionElements) {
        const ID = questionEl.data.ID.toString();

        let inputs;
        switch (questionEl.data.type) {
        case "shortAnswer":
            questionEl.input.value = answers[ID];
            break;
        case "singleChoice":
        case "multiChoice":
            inputs = Array.from(questionEl.inputsEl.getElementsByTagName("input"));
            for (const input of inputs) {
                if (answers[ID].includes(input.value))
                    input.checked = true;
            }
            break;
        case "fillIn":
            inputs = Array.from(questionEl.inputsEl.children);
            for (let i = 0; i < inputs.length; i++) {
                inputs[i].value = answers[ID][i];
            }
        }
    }
}

export function gradeAnswers(solutions)
{
    const questionElements
        = Array.from(document.getElementsByClassName("question-element"));

    for (const questionEl of questionElements) {
        const ID = questionEl.data.ID;

        let inputs;
        switch (questionEl.data.type) {
        case "shortAnswer":
            if (questionEl.input.value === solutions[ID])
                questionEl.input.classList.add("correct");
            else {
                questionEl.input.classList.add("incorrect");
                questionEl.input.title = solutions[ID];
                questionEl.input.style.cursor = "help";
            }
            break;
        case "singleChoice":
        case "multiChoice":
            inputs = Array.from(questionEl.inputsEl.getElementsByTagName("input"));
            for (let i = 0; i < inputs.length; i++) {
                if (solutions[ID].includes(inputs[i].value))
                    inputs[i].parentElement.classList.add("correct");
                else if (inputs[i].checked)
                    inputs[i].parentElement.classList.add("incorrect");
            }
            break;
        case "fillIn":
            inputs = Array.from(questionEl.inputsEl.children);
            for (let i = 0; i < inputs.length; i++) {
                if (inputs[i].value === solutions[ID][i])
                    inputs[i].classList.add("correct");
                else {
                    inputs[i].classList.add("incorrect");
                    inputs[i].title = solutions[ID][i];
                    inputs[i].style.cursor = "help";
                }
            }
        }
    }
}
