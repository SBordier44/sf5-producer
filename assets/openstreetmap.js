if ($('#map').length) {
    let carte = undefined
    let markers = L.markerClusterGroup();
    let homeIcon = L.icon({
        iconUrl: "images/osm/map-home-icon.png",
        iconSize: [50, 50],
        iconAnchor: [25, 50],
        popupAnchor: [0, -50]
    })

    L.Icon.Default.prototype.options.imagePath = 'images/osm/'

    if ('geolocation' in navigator) {
        navigator.geolocation.getCurrentPosition(position => {
            carte = L.map('map', {attributionControl: false}).setView([position.coords.latitude, position.coords.longitude], 12)
            let marker = L.marker([position.coords.latitude, position.coords.longitude], {
                icon: homeIcon
            }).addTo(carte)
            marker.bindPopup("<p class='font-weight-bold'>Chez moi</p>")
            renderMap(carte)
        })
    } else {
        carte = L.map('map', {attributionControl: false}).setView([48.86199992728625, 2.3384754313114295], 12)
        renderMap(carte)
    }

    function renderMap(carte) {
        L.tileLayer('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
            attribution: 'données @ <a href="//osm.org/copyright" target="_blank">OpenStreetMap</a>/ODbL - rendu <a href="//openstreetmap.fr" target="_blank">OSM France</a>',
            minZoom: 6,
            maxZoom: 20
        }).addTo(carte)
        fetch('/farm/all')
            .then(res => res.json())
            .then(farms => {
                farms.forEach(farm => {
                    let marker = L.marker([farm.address.position.latitude, farm.address.position.longitude]).addTo(carte)
                    marker.bindPopup(
                        '<p class="font-weight-bold">'
                        + farm.name + ' - '
                        + farm.address.zipCode
                        + ' '
                        + farm.address.city
                        + '</p><p>'
                        + farm.description
                        + '</p>'
                    )
                    marker.on('click', function (e) {
                        window.location.href = '/farm/' + farm.slug + '/show'
                    })
                    marker.on('mouseover', function (e) {
                        this.openPopup();
                    })
                    marker.on('mouseout', function (e) {
                        this.closePopup();
                    });
                    markers.addLayer(marker)
                })
            })
        carte.addLayer(markers)
    }
}
