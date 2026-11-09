import * as bootstrap from 'bootstrap';
import AjaxRequest from './ajax-request';
import Gmap from './gmap-adapter';
import Osm from './osm-adapter';

export default class MondialRelay {
    constructor(options = {}) {
        // Détection du formulaire Sylius 2 (ou fallback Sylius 1)
        this.shippingMethodForm = document.querySelector(
            'form[name="sylius_shop_checkout_select_shipping"], form[name="sylius_checkout_select_shipping"]'
        );
        this.modalEl = document.getElementById('modal-mondial-relay');

        if (!this.shippingMethodForm || !this.modalEl) {
            return;
        }

        this.bsModal = bootstrap.Modal.getOrCreateInstance(this.modalEl);
        this.itemsPerPage = options.itemsPerPage || 6;
        this.currentPage = 1;
        this.resultsSet = [];
        this.currentPickupPoint = null;
        this.mapProvider = this.modalEl.getAttribute('data-map-provider');
        this.mapAdapter = null;

        this.debounce = (func, delay = 300) => {
            let timeout;
            return (...args) => {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), delay);
            };
        };

        this.initEventListeners();
        this.checkInitialState();
        this.loadCurrentPoint();
    }

    initEventListeners() {
        // Redimensionnement de la carte lorsque la modale Bootstrap s'affiche
        this.modalEl.addEventListener('shown.bs.modal', () => {
            if (this.mapAdapter) {
                this.mapAdapter.resize();
            }
        });

        // Nettoyage à la fermeture
        this.modalEl.addEventListener('hidden.bs.modal', () => {
            this.clearModalContent();
        });

        // Changement de méthode de livraison
        this.shippingMethodForm.addEventListener('change', () => {
            const currentPointWrapper = document.getElementById('current-pickup-point');
            if (this.isMondialRelayMethodSelected()) {
                if (currentPointWrapper) currentPointWrapper.style.display = 'block';
                if (!this.currentPickupPoint) {
                    this.showModal();
                }
            } else {
                if (currentPointWrapper) currentPointWrapper.style.display = 'none';
                const input = this.shippingMethodForm.querySelector('input.smr-pickup-point-id');
                if (input) input.value = '';
                this.currentPickupPoint = null;
            }
        });

        // Bouton de validation dans la modal
        const selectBtn = this.modalEl.querySelector('#mr-select-btn');
        if (selectBtn) {
            selectBtn.addEventListener('click', () => {
                if (this.currentPickupPoint) {
                    this.setCheckoutPickupPoint();
                    this.bsModal.hide();
                }
            });
        }

        // Clics délégués (Ouvrir modal, pagination, géolocalisation, etc.)
        document.addEventListener('click', (event) => {
            // Ouvrir modal
            if (event.target.closest('[data-load-pickup-point-list]')) {
                event.preventDefault();
                this.showModal();
                return;
            }

            // Pagination
            const pageBtn = event.target.closest('button[data-page]');
            if (pageBtn && this.modalEl.contains(pageBtn)) {
                this.showPage(parseInt(pageBtn.getAttribute('data-page'), 10));
                return;
            }

            // Géolocalisation
            if (event.target.closest('button[data-mr-geolocalisation]')) {
                this.geolocation();
                return;
            }

            // Sélection d'un point dans la liste
            const relayItem = event.target.closest('.relay-point-list-item');
            if (relayItem && this.modalEl.contains(relayItem)) {
                this.selectPickupPoint(relayItem.getAttribute('data-relay-point-id'));
                return;
            }

            // Autocomplete
            const autoItem = event.target.closest('[data-autocomplete-item]');
            if (autoItem && this.modalEl.contains(autoItem)) {
                this.selectAutocompleteItem(autoItem);
            }
        });

        // Soumission du formulaire de recherche dans la modal
        document.addEventListener('submit', (event) => {
            if (event.target.matches('form.search-pickup-point-form')) {
                event.preventDefault();
                event.stopPropagation();
                this.search();
            }
        });

        // Autocomplete sur le code postal
        document.addEventListener('keyup', (event) => {
            if (event.target.getAttribute('data-mr-autocomplete')) {
                this.debounce(() => this.autocomplete(), 300)();
            }
        });
    }

    checkInitialState() {
        const currentPointWrapper = document.getElementById('current-pickup-point');
        if (currentPointWrapper && this.isMondialRelayMethodSelected()) {
            currentPointWrapper.style.display = 'block';
        }
    }

    isMondialRelayMethodSelected() {
        return this.shippingMethodForm.querySelector('input[type="radio"][name$="[method]"][data-mr="true"]:checked') !== null;
    }

    loadCurrentPoint() {
        const input = this.shippingMethodForm.querySelector('input.smr-pickup-point-id');
        if (input && input.value) {
            this.currentPickupPoint = input.value;
            this.setCheckoutPickupPoint();
        }
    }

    setCheckoutPickupPoint() {
        const input = this.shippingMethodForm.querySelector('input.smr-pickup-point-id');
        const currentPointWrapper = document.getElementById('current-pickup-point');
        if (input) input.value = this.currentPickupPoint;

        const findUrl = this.modalEl.getAttribute('data-find-url');
        const request = new AjaxRequest(findUrl, 'GET', { pickupPointId: this.currentPickupPoint });

        request.send().then((response) => {
            if (currentPointWrapper) {
                currentPointWrapper.innerHTML = response;
                currentPointWrapper.style.display = 'block';
            }
        });
    }

    showModal() {
        const loadUrl = this.modalEl.getAttribute('data-load-url');
        const request = new AjaxRequest(loadUrl, 'GET');

        request.send().then((rawResponse) => {
            const response = JSON.parse(rawResponse);
            const content = this.modalEl.querySelector('.content');
            content.innerHTML = response.form || '';

            this.createMapAdapter();
            this.bsModal.show();
            this.onSearchResultsUpdated(response.points || []);
        });
    }

    clearModalContent() {
        this.mapAdapter = null;
    }

    createMapAdapter() {
        const mapContainer = this.modalEl.querySelector('#pickup-points-map');
        if (!mapContainer) return;

        const params = {
            el: mapContainer,
            icons: {
                markerDefault: this.modalEl.getAttribute('data-marker'),
                markerSelected: this.modalEl.getAttribute('data-marker-selected'),
            },
            translations: {
                choose: this.modalEl.getAttribute('data-choose-label'),
            },
            onSelectMarker: (id) => this.selectPickupPoint(id),
        };

        if ('open_street_map' === this.mapProvider) {
            this.mapAdapter = new Osm(params);
        } else if ('google' === this.mapProvider) {
            this.mapAdapter = new Gmap(params);
        }
    }

    selectPickupPoint(id) {
        this.currentPickupPoint = id;

        // Mise à jour visuelle des éléments de la liste
        const items = this.modalEl.querySelectorAll('.relay-point-list-item');
        items.forEach((item) => {
            if (item.getAttribute('data-relay-point-id') === id) {
                item.classList.add('active', 'border-primary');
            } else {
                item.classList.remove('active', 'border-primary');
            }
        });

        if (this.mapAdapter) {
            this.mapAdapter.selectMarker(id);
        }

        // Activation du bouton Choisir
        const selectBtn = this.modalEl.querySelector('#mr-select-btn');
        if (selectBtn) {
            selectBtn.removeAttribute('disabled');
        }
    }

    search() {
        const form = this.modalEl.querySelector('form.search-pickup-point-form');
        if (!form) return;

        const request = new AjaxRequest(
            form.getAttribute('action'),
            form.getAttribute('method').toUpperCase(),
            new URLSearchParams(new FormData(form))
        );

        request.send().then((response) => {
            this.onSearchResultsUpdated(JSON.parse(response));
        });
    }

    onSearchResultsUpdated(points) {
        this.resultsSet = points;
        this.currentPage = 1;

        const resultsList = this.modalEl.querySelector('.pickup-points-results-list');
        const noResults = this.modalEl.querySelector('.pickup-points-no-results');
        const pagination = this.modalEl.querySelector('.pickup-points-pagination');

        if (!points || points.length === 0) {
            if (this.mapAdapter) this.mapAdapter.clearMarkers();
            if (resultsList) resultsList.innerHTML = '';
            if (noResults) noResults.style.display = 'block';
            if (pagination) pagination.innerHTML = '';
            return;
        }

        if (noResults) noResults.style.display = 'none';
        this.showSearchResults();
        this.renderPagination();
    }

    showSearchResults() {
        const resultsList = this.modalEl.querySelector('.pickup-points-results-list');
        if (!resultsList) return;

        resultsList.innerHTML = '';
        const offset = (this.currentPage - 1) * this.itemsPerPage;
        const pagePoints = this.resultsSet.slice(offset, offset + this.itemsPerPage);

        const listGroup = document.createElement('div');
        listGroup.className = 'list-group list-group-flush';

        pagePoints.forEach((point) => {
            const item = document.createElement('a');
            item.href = '#';
            item.className = 'list-group-item list-group-item-action relay-point-list-item py-2';
            item.setAttribute('data-relay-point-id', point.id);

            item.innerHTML = `
                <div class="d-flex w-100 justify-content-between align-items-center">
                    <strong class="mb-1 text-primary">${point.label}</strong>
                    <small class="badge bg-light text-dark">${point.distance ? point.distance + ' m' : ''}</small>
                </div>
                <p class="mb-1 text-muted small">${point.address}, ${point.zipCode || ''} ${point.city || ''}</p>
            `;

            listGroup.appendChild(item);
        });

        resultsList.appendChild(listGroup);

        if (this.mapAdapter) {
            this.mapAdapter.updateMarkers(pagePoints);
        }
    }

    renderPagination() {
        const pagination = this.modalEl.querySelector('.pickup-points-pagination');
        if (!pagination) return;
        pagination.innerHTML = '';

        const pagesCount = Math.ceil(this.resultsSet.length / this.itemsPerPage);
        if (pagesCount <= 1) return;

        for (let i = 1; i <= pagesCount; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${i === this.currentPage ? 'active' : ''}`;
            li.innerHTML = `<button type="button" class="page-link" data-page="${i}">${i}</button>`;
            pagination.appendChild(li);
        }
    }

    showPage(page) {
        this.currentPage = page;
        this.showSearchResults();
        this.renderPagination();
    }

    geolocation() {
        if (!navigator.geolocation || !this.mapAdapter) return;
        const btn = this.modalEl.querySelector('button[data-mr-geolocalisation]');
        if (btn) btn.disabled = true;

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const pos = { lat: position.coords.latitude, lng: position.coords.longitude };
                this.mapAdapter.setMapCenter(pos);
                this.mapAdapter.getZipCodeByPosition(pos).then((zipCode) => {
                    if (btn) btn.disabled = false;
                    const zipInput = this.modalEl.querySelector('input[data-mr-autocomplete]');
                    if (zipInput) {
                        zipInput.value = zipCode;
                        this.search();
                    }
                }).catch(() => { if (btn) btn.disabled = false; });
            },
            () => { if (btn) btn.disabled = false; }
        );
    }

    autocomplete() {
        const input = this.modalEl.querySelector('input[data-mr-autocomplete]');
        const dropdown = this.modalEl.querySelector('.search-pickup-point-autocomplete .dropdown-menu');
        const autoUrl = this.modalEl.getAttribute('data-autocomplete-url');
        if (!input || !dropdown || !autoUrl || input.value.length < 2) return;

        new AjaxRequest(autoUrl, 'GET', { query: input.value }).send().then((raw) => {
            const places = JSON.parse(raw);
            dropdown.innerHTML = '';
            if (places.length === 0) {
                dropdown.style.display = 'none';
                return;
            }

            places.forEach((place) => {
                const li = document.createElement('li');
                li.innerHTML = `<a class="dropdown-item" href="#" data-autocomplete-item="${place.id}">${place.label}</a>`;
                dropdown.appendChild(li);
            });
            dropdown.style.display = 'block';
        });
    }

    selectAutocompleteItem(item) {
        const input = this.modalEl.querySelector('input[data-mr-autocomplete]');
        const suggestionInput = this.modalEl.querySelector('input[name$="[suggestion]"]');
        const dropdown = this.modalEl.querySelector('.search-pickup-point-autocomplete .dropdown-menu');

        if (input) input.value = item.textContent.trim();
        if (suggestionInput) suggestionInput.value = item.getAttribute('data-autocomplete-item');
        if (dropdown) dropdown.style.display = 'none';

        this.search();
    }
}
