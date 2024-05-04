class QuestionElement extends HTMLDivElement {
    constructor(questionData) {
        super();

        for (const [key, value] of Object.entries(questionData)) {
            if (Array.isArray(value))
                this.dataset[key] = JSON.stringify(value);
            else
                this.dataset[key] = value;
        }
    }

    createMultiInput(content)
    {
        const radioContainer = document.createElement("label");
        radioContainer.classList.add("radio-btn");
        this.inputsEl.appendChild(radioContainer);

        const offeredAnswerSpan = document.createElement("span");
        offeredAnswerSpan.innerText = content;
        radioContainer.htmlFor = this.dataset.ID.toString() + Math.random();
        radioContainer.appendChild(offeredAnswerSpan);

        const radioBtn = document.createElement("input");
        if (this.dataset.type === "multiChoice")
            radioBtn.type = "checkbox";
        else
            radioBtn.type = "radio";
        radioBtn.value = content;
        radioBtn.name = this.dataset.ID.toString();
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
        this.input.name = this.dataset.ID.toString();
        this.input.className = "input";
        this.inputsEl.appendChild(this.input);
    }

    connectedCallback() {
        const template = document.getElementById("template-question-element");
        this.appendChild(template.content.cloneNode(true));
        this.className = "question-element";

        this.titleEl = this.querySelector(".title");
        this.contentEl = this.querySelector(".content");
        this.inputsEl = this.querySelector(".inputs");

        this.titleEl.innerText = this.dataset.title;

        switch (this.dataset.type) {
            case "singleChoice":
            case "multiChoice":
                const offeredAnswers = JSON.parse(this.dataset.offeredAnswers);
                for (const offeredAnswer of offeredAnswers)
                    this.createMultiInput(offeredAnswer);
                break;
            case "shortAnswer":
                this.createShortAnswerInput();
                break;
            case "fillIn":
                const textFragments = this.dataset.partialText.split("@@@");

                for (const textFragment of textFragments) {
                    this.inputsEl.insertAdjacentText("beforeend", textFragment);

                    this.createShortAnswerInput();
                }
                this.inputsEl.lastElementChild.remove();
        }
    }
}

customElements.define("question-element", QuestionElement, { extends: "div" });
