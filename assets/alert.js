export class SnowAlert {

    #template = "<div id=\"snowTrick_message\" class=\"alert alert-dismissible fade show\" role=\"alert\">\n" +
        "        %content%\n" +
        "        <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button>\n" +
        "    </div>";

    constructor(message, level = "alert-primary", timer = 0) {
        this.message = message;
        this.level = level;
        this.timer = timer;

        this.messageElement = this.#template;

        this.messageElement = this.messageElement.replace('%content%', this.message);

        let parser = new DOMParser();
        this.messageElement = parser.parseFromString(this.messageElement, 'text/html');
        this.messageElement = this.messageElement.body.firstChild;

        this.messageElement.classList.toggle(this.level);
    }

    show() {
        const navbar = document.getElementById("navbar_header");
        navbar.insertAdjacentElement('afterend', this.messageElement);
        this.messageElement.scrollIntoView({ behavior: "smooth"});

        if (this.timer === 0) {
            return;
        }

        setInterval(() => {
            this.messageElement.hidden = true;
            }, this.timer);
    }
}