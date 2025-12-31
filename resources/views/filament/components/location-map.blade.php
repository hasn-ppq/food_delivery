<div
    wire:ignore
    x-data="locationMap()"
    x-init="init()"
    class="w-full"
>
    <div x-ref="map" class="h-[400px] rounded-lg border"></div>
</div>

@once
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
function locationMap() {
    return {
        map: null,
        marker: null,

        lat: @entangle('latitude').defer,
        lng: @entangle('longitude').defer,

        init() {
            if (this.map) return; // ❌ منع إعادة التهيئة

            const defaultLat = this.lat ?? 33.3152;
            const defaultLng = this.lng ?? 44.3661;

            this.map = L.map(this.$refs.map, {
                zoomControl: true,
                inertia: true,
            }).setView([defaultLat, defaultLng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(this.map);

            this.marker = L.marker([defaultLat, defaultLng], {
                draggable: true
            }).addTo(this.map);

            // 📍 عند سحب الماركر
            this.marker.on('dragend', (e) => {
                this.updateLocation(e.target.getLatLng());
            });

            // 🖱️ عند الضغط على الخريطة
            this.map.on('click', (e) => {
                this.marker.setLatLng(e.latlng);
                this.updateLocation(e.latlng);
            });

            // 📡 تحديد الموقع الحالي (مرة وحدة)
            if (!this.lat || !this.lng) {
                this.locateUser();
            }
        },

        locateUser() {
            if (!navigator.geolocation) return;

            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    const coords = {
                        lat: pos.coords.latitude,
                        lng: pos.coords.longitude
                    };

                    this.marker.setLatLng(coords);
                    this.map.flyTo(coords, 16, {
                        animate: true,
                        duration: 1.2 // 🎯 حركة سلسة
                    });

                    this.updateLocation(coords);
                },
                () => {
                    // رفض الإذن → لا شي
                },
                {
                    enableHighAccuracy: true,
                    timeout: 8000
                }
            );
        },

        updateLocation(coords) {
            this.lat = coords.lat;
            this.lng = coords.lng;
        }
    }
}
</script>
@endonce
