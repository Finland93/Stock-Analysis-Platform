function checkWebP() {
    var supportsWebP = false;
    var webP = new Image();
    webP.src = "data:image/webp;base64,UklGRiIAAABXRUJQVlA4IBYAAAAwAQCdASoBAAEADsD+JaQAA3AAAAAA";
    webP.onload = function () {
        supportsWebP = true;
    };
    setTimeout(function () {
        if (supportsWebP) {
            document.querySelector("img").src = "image.webp";
        }
    }, 100);
}
window.addEventListener("load", checkWebP);
