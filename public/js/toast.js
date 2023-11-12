class Toast {
  constructor(message, type = "info", options = {}) {
    this.message = message;
    this.type = type;
    this.options = options;
    this.setOptions();
    this.display();
  }

  getContainer() {
    const container = document.createElement("div");
    container.id = "toastContainer";
    return container;
  }

  setOptions() {
    this.container =
      document.getElementById("#toastContainer") ?? this.getContainer();
  }

  getToast() {
    return `<div class="toast show align-items-center text-bg-${this.type} border-0" style="z-index: auto;" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
            ${this.message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>`;
  }

  display() {
    const container = this.container;
    container.innerHTML = this.getToast();
    container.style.position = "fixed";
    container.style.bottom = "15px";
    container.style.right = "15px";
    container.style.zIndex = "auto";
    document.body.insertAdjacentElement("beforeend", container);
    return;
  }
}
