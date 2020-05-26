const flash = document.getElementById('flash-wrapper');


setTimeout(() => {
    flash.style.height = '55px';
    flash.style.margin = '0 0 25px 0';
}, 50);

setTimeout(() => {
    flash.style.height = '0';
    flash.style.margin = '0';
}, 2050);