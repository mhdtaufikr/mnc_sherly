import './bootstrap'
import '@fortawesome/fontawesome-free/js/all.min.js'

import Alpine from 'alpinejs'
import collapse from '@alpinejs/collapse'
import Swal from 'sweetalert2'
import $ from 'jquery'
import 'datatables.net-dt'
import { Calendar } from '@fullcalendar/core'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import listPlugin from '@fullcalendar/list'

window.$ = window.jQuery = $
window.Swal = Swal
window.FullCalendar = {
  Calendar,
  dayGridPlugin,
  interactionPlugin,
  listPlugin,
}

Alpine.plugin(collapse)
window.Alpine = Alpine
Alpine.start()
