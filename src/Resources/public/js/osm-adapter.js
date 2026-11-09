import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import AjaxRequest from './ajax-request';

export default class OsmAdapter {
    constructor(options) {
        this.el = options.el;
        this.markers = [];
        this.icons = {
            markerSelected: L.icon({
                iconUrl: options.icons.markerSelected,
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
            }),
            markerDefault: L.icon({
                iconUrl: options.icons.markerDefault,
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
            }),
        };
        this.translations = {
            choose: options.translations.choose,
        };
        this.onSelectMarker = options.onSelectMarker || function() {};

        this.createMap();
    }

    createMap() {
        this.map = L.map(this.el, {
            center: [46.603354, 1.888334], // Centre de la France
            zoom: 6,
        });

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a>',
        }).addTo(this.map);

        this.resize();
    }

    resize() {
        if (!this.map) return;
        setTimeout(() => this.map.invalidateSize(), 50);
        setTimeout(() => this.map.invalidateSize(), 300);
    }

    updateMarkers(markers) {
        this.clearMarkers();
        if (!markers || markers.length === 0) return;

        const points = [];

        markers.forEach((markerData) => {
            const lat = parseFloat(markerData.lat);
            const lng = parseFloat(markerData.lng);
            if (isNaN(lat) || isNaN(lng)) return;

            const latLng = L.latLng(lat, lng);
            points.push(latLng);

            const marker = L.marker(latLng, {
                title: markerData.label,
                icon: markerData.selected ? this.icons.markerSelected : this.icons.markerDefault,
            }).addTo(this.map);

            marker.on('click', () => {
                this.onSelectMarker(markerData.id);
            });

            this.markers.push({ id: markerData.id, marker });
        });

        if (points.length > 0) {
            this.map.fitBounds(L.latLngBounds(points), { padding: [30, 30] });
        }
    }

    clearMarkers() {
        this.markers.forEach(item => item.marker.removeFrom(this.map));
        this.markers = [];
    }

    selectMarker(id) {
        this.markers.forEach((item) => {
            if (item.id === id) {
                item.marker.setIcon(this.icons.markerSelected);
            } else {
                item.marker.setIcon(this.icons.markerDefault);
            }
        });
    }

    setMapCenter(position) {
        if (this.map) {
            this.map.setView(L.latLng(parseFloat(position.lat), parseFloat(position.lng)), 14);
        }
    }

    getZipCodeByPosition(position) {
        return new Promise((resolve, reject) => {
            const request = new AjaxRequest('https://nominatim.openstreetmap.org/reverse', 'GET', {
                format: 'jsonv2',
                lat: position.lat,
                lon: position.lng,
            });
            request.send().then((rawResponse) => {
                const response = JSON.parse(rawResponse);
                if (response && response.address && response.address.postcode) {
                    resolve(response.address.postcode);
                } else {
                    reject();
                }
            }).catch(reject);
        });
    }
}
