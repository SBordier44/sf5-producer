import './styles/app.scss';
import 'popper.js';
import 'leaflet';
import 'leaflet.markercluster';
import './openstreetmap';

import 'bootstrap';

$('.btn-remove-file').click(e => {
    $(e.currentTarget.dataset.target).val('')
})

$('.input-qty').change((e) => {
    e.preventDefault()
    $('form[name=cart]').submit();
})

$("input[type=file]").change(function (e) {
    $(this).next('.custom-file-label').text(e.target.files[0].name);
})
