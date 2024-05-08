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
