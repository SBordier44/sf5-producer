import './app'

let map;

function loadMap(center) {
    map = new google.maps.Map(document.getElementById('map'), {
        center,
        zoom: 10
    })

    fetch('/farm/all')
        .then(res => res.json())
        .then(farms => {
            farms.forEach(farm => {
                let marker = new google.maps.Marker({
                    position: {
                        lat: farm.address.position.latitude,
                        lng: farm.address.position.longitude
                    },
                    map,
                    draggable: true,
                    animation: google.maps.Animation.DROP,
                    title: farm.name
                })
                marker.addListener('click', () => {
                    window.location.href = '/farm/' + farm.slug + '/show'
                })
            })
        })
}

window.initMap = () => {
    if ('geolocation' in navigator) {
        navigator.geolocation.getCurrentPosition(position => {
            loadMap({
                lat: position.coords.latitude,
                lng: position.coords.longitude
            })
        })
    } else {
        loadMap({
            lat: 48.441049,
            lng: 1.546233
        })
    }
    // Calcul distance entre deux adresses
    // new google.maps.DistanceMatrixService().getDistanceMatrix({
    //     origins: [new google.maps.LatLng('47.192892', '-1.4798137000000002')],
    //     destinations: [new google.maps.LatLng('47.4175439728250', '-1.8472411726673')],
    //     travelMode: google.maps.TravelMode.DRIVING,
    //     unitSystem: google.maps.UnitSystem.METRIC
    // }, callback)
    //
    // function callback(response, status) {
    //     console.log(response)
    //     console.log(status)
    // }
}

navigator.geolocation.getCurrentPosition(position => {
    console.log('latitude : ' + position.coords.latitude)
    console.log('longitude : ' + position.coords.longitude)
});
