console.log("Astra Child Theme JS is running!");

document.addEventListener("DOMContentLoaded", function () {
    console.log("The DOM is ready!");
    initTypedJs();
});

function initTypedJs() {
    new Typed(".typed", {
        strings: ["visuel designer", "illustrator", "UI designer", "multimediedesigner"],
        typeSpeed: 75,
        loop: true,
    });
};
  