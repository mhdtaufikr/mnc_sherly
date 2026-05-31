import './bootstrap'
import '@fortawesome/fontawesome-free/js/all.min.js'

import Alpine from 'alpinejs'
import collapse from '@alpinejs/collapse'
import Swal from 'sweetalert2'
import $ from 'jquery'
import 'datatables.net-dt'

window.$ = window.jQuery = $
window.Swal = Swal

Alpine.plugin(collapse)
window.Alpine = Alpine
Alpine.start()