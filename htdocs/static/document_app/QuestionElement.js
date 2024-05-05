export class QuestionElement extends HTMLDivElement {
    constructor(questionData = null)
    {
        super();

        this.data = questionData;
        this.id = this.data.ID;
    }

    createMultiInput(content)
    {
        const radioContainer = document.createElement("label");
        radioContainer.classList.add("radio-btn");
        this.inputsEl.appendChild(radioContainer);

        const offeredAnswerSpan = document.createElement("span");
        offeredAnswerSpan.innerText = content;
        radioContainer.htmlFor = this.data.ID.toString() + Math.random();
        radioContainer.appendChild(offeredAnswerSpan);

        const radioBtn = document.createElement("input");
        if (this.data.type === "multiChoice")
            radioBtn.type = "checkbox";
        else
            radioBtn.type = "radio";
        radioBtn.value = content;
        radioBtn.name = this.data.ID.toString();
        radioBtn.id = radioContainer.htmlFor;
        radioContainer.appendChild(radioBtn);

        const checkmark = document.createElement("span");
        checkmark.classList.add(radioBtn.type);
        radioContainer.appendChild(checkmark);
    }

    createShortAnswerInput()
    {
        this.input = document.createElement("input");
        this.input.type = "text";
        this.input.name = this.data.ID.toString();
        this.input.className = "input";
        this.inputsEl.appendChild(this.input);
    }

    connectedCallback()
    {
        const template = document.getElementById("template-question-element");
        this.appendChild(template.content.cloneNode(true));
        this.className = "question-element";

        this.titleEl = this.querySelector(".title");
        this.headerBtns = this.querySelector(".header-buttons");
        this.contentEl = this.querySelector(".content");
        this.inputsEl = this.querySelector(".inputs");

        this.titleEl.innerText = this.data.title;

        switch (this.data.type) {
        case "singleChoice":
        case "multiChoice":
            for (const offeredAnswer of this.data.offeredAnswers)
                this.createMultiInput(offeredAnswer);
            break;
        case "shortAnswer":
            this.createShortAnswerInput();
            break;
        case "fillIn":
            const textFragments = this.data.partialText.split("@@@");

            for (const textFragment of textFragments) {
                this.inputsEl.insertAdjacentText("beforeend", textFragment);

                this.createShortAnswerInput();
            }
            this.inputsEl.lastElementChild.remove();
        }
    }
}

customElements.define("question-element", QuestionElement, { extends: "div" });
