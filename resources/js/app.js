import 'flowbite';
import Swal from 'sweetalert2';
import './bootstrap';

import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';

window.Swal = Swal;
window.Alpine = Alpine;

Alpine.plugin(focus);

Alpine.start();
