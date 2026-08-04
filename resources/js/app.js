import './bootstrap';
import * as bootstrap from 'bootstrap';
import Swal from 'sweetalert2';
import { Chart } from 'chart.js/auto';

window.bootstrap = bootstrap;
window.Swal = Swal;
window.Chart = Chart;

// Confirmação SweetAlert2 para qualquer formulário de eliminação do painel —
// um único sítio em vez de repetir o mesmo script em cada view (Etapa 7,
// ações destrutivas usam sempre soft delete, nunca eliminação silenciosa).
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.confirmar-eliminacao').forEach(function (form) {
        form.addEventListener('submit', function (evento) {
            evento.preventDefault();
            Swal.fire({
                title: 'Eliminar este registo?',
                text: 'Esta ação pode ser revertida por um administrador diretamente na base de dados, mas não a partir daqui.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar',
            }).then(function (resultado) {
                if (resultado.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
