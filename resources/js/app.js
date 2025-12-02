import './bootstrap';

import Swal from 'sweetalert2';
import ApexCharts from 'apexcharts';

// Make Swal and ApexCharts available globally
window.Swal = Swal;
window.ApexCharts = ApexCharts;

// Livewire 3 maneja Alpine automáticamente, no lo iniciemos manualmente
// Si necesitamos Alpine para componentes no-Livewire, lo configuramos así:
document.addEventListener('livewire:init', () => {
    // Aquí podemos configurar plugins de Alpine si es necesario
    console.log('Livewire initialized with Alpine');
});
