const centerSelector = document.querySelector('select');

centerSelector.addEventListener('change', (event) => {
    const selector = event.currentTarget;
    const url      = URL.parse(window.location.href);

    url.searchParams.set('ID_Centro', selector.value);

    window.location.assign(url);
})