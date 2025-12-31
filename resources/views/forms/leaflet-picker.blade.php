<div 
    wire:ignore
    x-data="{
        map: null,
        marker: null,
        lat: {{ $get('lat') ?? 'null' }},
        lng: {{ $get('lng') ?? 'null' }},
        initMap() {
            let defaultLat = this.lat ?? 33.3117;
            let defaultLng = this.lng ?? 44.3561;

            // إنشاء الخريطة
            this.map = new google.maps.Map($el, {
                zoom: 14,
                center: { lat: defaultLat, lng: defaultLng },
            });

            // إنشاء الماركر
            this.marker = new google.maps.Marker({
                position: { lat: defaultLat, lng: defaultLng },
                map: this.map,
                draggable: true,
            });

            // تحديث الإحداثيات أثناء السحب مع تحريك الخريطة
            this.marker.addListener('drag', (e) => {
                let lat = e.latLng.lat();
                let lng = e.latLng.lng();

                this.updateCoordinates(lat, lng);
                
                // تحريك الخريطة بشكل سلس مع الماركر
                this.map.panTo({ lat, lng });
            });

            // Dragend event لتحديث نهائي
            this.marker.addListener('dragend', (e) => {
                this.updateCoordinates(e.latLng.lat(), e.latLng.lng());
            });

            // Click على الخريطة لتغيير موقع الماركر
            this.map.addListener('click', (e) => {
                this.marker.setPosition(e.latLng);
                this.map.panTo(e.latLng);
                this.updateCoordinates(e.latLng.lat(), e.latLng.lng());
            });

            // تحديد الموقع التلقائي (GPS)
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition((pos) => {
                    let lat = pos.coords.latitude;
                    let lng = pos.coords.longitude;

                    this.map.panTo({ lat, lng });
                    this.marker.setPosition({ lat, lng });
                    this.updateCoordinates(lat, lng);
                });
            }
        },
        updateCoordinates(lat, lng) {
            this.lat = lat;
            this.lng = lng;
            $wire.set('data.lat', lat);
            $wire.set('data.lng', lng);
        },
        confirmLocation() {
            alert('تم حفظ الموقع!\nLatitude: ' + this.lat + '\nLongitude: ' + this.lng);
        }
    }"
    x-init="initMap()"
    style="height: 420px; border-radius: 12px; position: relative;"
>
    <!-- زر تأكيد الموقع -->
    <button 
        x-on:click="confirmLocation()" 
        class="absolute top-2 right-2 bg-blue-600 text-white px-4 py-2 rounded shadow"
    >
        تأكيد الموقع
    </button>
</div>

@once
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBOB1vBd6n8o29warRgpRsu18X1DRFHz8Y"></script>
@endonce
