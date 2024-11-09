import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import $ from 'jquery';
import Swal from 'sweetalert2';
import Chart from 'chart.js/auto';
import { jsPDF } from 'jspdf';

window.Swal = Swal;
window.Chart = Chart;

function openNav() {
    document.getElementById("sidebar").style.width = "250px";
    document.getElementById("main").style.marginLeft = "250px";
}

function closeNav() {
    document.getElementById("sidebar").style.width = "0";
    document.getElementById("main").style.marginLeft = "0";
}

$(document).ready(function() {
    $('#menu').click(function() {
        openNav();
    });

    $('.closebtn').click(function() {
        closeNav();
    });

    let timeout;

    function resetTimer() {
        console.log('escucha');
        clearTimeout(timeout);
        timeout = setTimeout(logout, 900000); // 15 minutes in milliseconds
    }

    function logout() {
        console.log('llama');
        $.ajax({
            url: '/logout',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function() {
                window.location.href = '/';
            },
            error: function(xhr, status, error) {
                console.log('Error:', error);
            }
        });        
    }

    // Mostrar errores de validación desde el servidor usando SweetAlert
    if (window.validationErrors && window.validationErrors.length > 0) {
        let errorMessage = '<ul>';
        window.validationErrors.forEach(function(error) {
            errorMessage += `<li>${error}</li>`;
        });
        errorMessage += '</ul>';
        Swal.fire({
            title: 'Errores de validación',
            html: errorMessage,
            icon: 'error',
        });
    }

    $(document).on('mousemove keydown click scroll', resetTimer);
    resetTimer();
});

window.openNav = openNav;
window.closeNav = closeNav;
