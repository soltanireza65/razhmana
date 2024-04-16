function c() {
    const c = document.querySelectorAll("img[data-src]");
    c.forEach(c => {
        const n = c.getBoundingClientRect().top;
        const o = c.getBoundingClientRect().bottom;
        const t = window.innerHeight;
        const s = 0;
        if (n < t && o > s) {
            c.src = c.dataset.src;
            c.removeAttribute("data-src")
        }
    })
}
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function (){c();} , 500)
});
window.addEventListener("scroll", c);