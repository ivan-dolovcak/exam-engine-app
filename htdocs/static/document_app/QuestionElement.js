class QuestionElement extends HTMLDivElement {
    constructor(questionData) {
        super();

        for (const [key, value] of Object.entries(questionData)) {
            this.dataset[key] = value;
        }
    }

    connectedCallback() {
        const template = document.getElementById("template-question-element");
        this.appendChild(template.content.cloneNode(true));

        const titleElement = this.querySelector(".title");
        const contentElement = this.querySelector(".content");

        titleElement.innerText = this.dataset.title;
    }
}

customElements.define("question-element", QuestionElement, { extends: "div" });
