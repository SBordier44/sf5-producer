import './styles/app.scss';
import 'popper.js';
import 'bootstrap';

$('.btn-remove-file').click(e => {
    $(e.currentTarget.dataset.target).val('')
})
