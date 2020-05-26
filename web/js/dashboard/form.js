const esName = document.getElementById('esName');
const esPswd = document.getElementById('esPswd');
const esHash = document.getElementById('esHash');

esName.addEventListener('change', function () {
    esHash.value = btoa(esName.value + ":" + esPswd.value);
});

esPswd.addEventListener('change', function () {
    esHash.value = btoa(esName.value + ":" + esPswd.value);
});

document.addEventListener('DOMContentLoaded', function () {
    let tmp = atob(esHash.value);
    tmp = tmp.split(":");
    
    if (tmp.length > 1) {
        esName.value = tmp[0];
        esPswd.value = tmp[1];
    } else {
        esName.value = '';
        esPswd.value = '';
    }
});
