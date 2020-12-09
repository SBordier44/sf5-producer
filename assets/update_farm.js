import './app'

class Map {
    constructor() {
        this.center = {
            lat: 48.441049,
            lng: 1.546233
        }
        this.map = null
        this.loadGeolocation()
    }

    loadGeolocation() {
        if ('geolocation' in navigator) {
            navigator.geolocation.getCurrentPosition(position => {
                this.center.lat = position.coords.latitude
                this.center.lng = position.coords.longitude
                this.loadMap()
            })
        } else {
            this.loadMap()
        }
    }

    loadMap() {
        this.map = new google.maps.Map(document.getElementById('map'), {
            center: this.center,
            zoom: 12
        })
        if ($('#farm_address_position_latitude').val() !== '' && $('#farm_address_position_longitude').val() !== '') {
            this.marker = new google.maps.Marker({
                position: {
                    lat: parseFloat($('#farm_address_position_latitude').val()),
                    lng: parseFloat($('#farm_address_position_longitude').val())
                },
                map: this.map,
                title: 'Mon Exploitation'
            })
        }

        this.map.addListener('click', this.getPosition.bind(this))
    }

    getPosition(e) {
        this.center.lat = e.latLng.lat()
        this.center.lng = e.latLng.lng()
        this.map.panTo(new google.maps.LatLng(this.center.lat, this.center.lng))
        this.marker = new google.maps.Marker({
            position: this.center,
            map: this.map,
            title: 'Mon Exploitation'
        })

        fetch(`https://maps.googleapis.com/maps/api/geocode/json?latlng=${this.marker.position.lat()},${this.marker.position.lng()}&key=${process.env.GOOGLE_MAP_API_KEY}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        })
            .then(res => res.json())
            .then(json => {
                console.log(json)
                let streetNumber = json.results[0].address_components.find(data => data.types.indexOf("street_number") >= 0)
                let street = json.results[0].address_components.find(data => data.types.indexOf("route") >= 0)
                let zipCode = json.results[0].address_components.find(data => data.types.indexOf("postal_code") >= 0)
                let city = json.results[0].address_components.find(data => data.types.indexOf("locality") >= 0)
                let country = json.results[0].address_components.find(data => data.types.indexOf("country") >= 0)
                let region = json.results[0].address_components.find(data => data.types.indexOf("administrative_area_level_1") >= 0)
                let department = json.results[0].address_components.find(data => data.types.indexOf("administrative_area_level_2") >= 0)

                if (streetNumber && street) {
                    $('#farm_address_address1').val(streetNumber.long_name + ' ' + street.long_name)
                }
                if (zipCode) {
                    $('#farm_address_zipCode').val(zipCode.long_name)
                }
                if (city) {
                    $('#farm_address_city').val(city.long_name)
                }
                if (country) {
                    $('#farm_address_country').val(country.long_name)
                }
                if (region + department) {
                    $('#farm_address_region').val(department.long_name + ", " + region.long_name)
                }

                $('#farm_address_position_latitude').val(this.marker.position.lat())
                $('#farm_address_position_longitude').val(this.marker.position.lng())
            })
    }
}

window.initMap = () => {
    new Map()
}
