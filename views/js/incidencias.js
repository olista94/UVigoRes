const resourceSelector = document.getElementById('Recurso');

resourceSelector.addEventListener('change', (event) => {
    const selector = event.currentTarget;
    const selected = Array.from(selector.selectedOptions).shift();
    const centerId = selected.dataset.center;

    const url = URL.parse(window.location.href);

    url.searchParams.set('ID_Centro', centerId);
    url.searchParams.set('ID_Recurso', selector.value);

    window.location.assign(url);
})