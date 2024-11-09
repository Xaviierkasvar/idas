import { jsPDF } from 'jspdf';

document.addEventListener('DOMContentLoaded', function () {
    const betTable = document.getElementById('betTable').getElementsByTagName('tbody')[0];
    const rows = Array.from(betTable.getElementsByTagName('tr'));
    const paginationControls = document.getElementById('paginationControls');
    const rowsPerPage = 10; // Número de filas por página
    let currentPage = 1;

    function setupPagination(filteredRows) {
        paginationControls.innerHTML = '';
        const totalPages = Math.ceil(filteredRows.length / rowsPerPage);

        for (let i = 1; i <= totalPages; i++) {
            const pageButton = document.createElement('button');
            pageButton.textContent = i;
            pageButton.classList.add('btn', 'btn-sm', 'btn-primary', 'me-1');
            if (i === currentPage) pageButton.classList.add('active');
            pageButton.addEventListener('click', function () {
                currentPage = i;
                displayPage(filteredRows, currentPage);
                setupPagination(filteredRows);
            });
            paginationControls.appendChild(pageButton);
        }
    }

    function displayPage(filteredRows, page) {
        betTable.innerHTML = '';
        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const rowsToShow = filteredRows.slice(start, end);

        rowsToShow.forEach(row => betTable.appendChild(row));
    }

    // Inicializa la primera visualización de página
    setupPagination(rows);
    displayPage(rows, currentPage);

    // Función para generar el PDF de la página actual
    document.getElementById('printButton').addEventListener('click', () => {
        printCurrentPage(rows.slice((currentPage - 1) * rowsPerPage, currentPage * rowsPerPage));
    });

    function printCurrentPage(data) {
        const pdf = new jsPDF();

        pdf.setFontSize(16);
        pdf.text("Betting details", 10, 10);

        const headers = ["ID", "Draw Number", "Bet Number", "Bet Amount", "User", "Date & Time"];
        let startY = 30;
        const rowHeight = 10;

        // Dibujar los encabezados de la tabla
        headers.forEach((header, index) => {
            pdf.text(header, 10 + (index * 30), startY);
        });
        startY += rowHeight;

        let totalBetAmount = 0;

        // Dibujar las filas de la tabla
        data.forEach(bet => {
            pdf.text(bet.cells[0].innerText, 10, startY);
            pdf.text(bet.cells[1].innerText, 40, startY);
            pdf.text(bet.cells[2].innerText, 70, startY);
            pdf.text(bet.cells[3].innerText, 100, startY);
            pdf.text(bet.cells[4].innerText, 130, startY);
            pdf.text(bet.cells[5].innerText, 160, startY);

            // Sumar el monto total de la apuesta
            totalBetAmount += parseFloat(bet.cells[3].innerText.replace(/\D/g, '')); // Eliminar caracteres no numéricos

            startY += rowHeight;
        });

        // Mostrar el monto total al final
        const formattedTotalBetAmount = totalBetAmount.toLocaleString("es-CO", { minimumFractionDigits: 0 });
        pdf.setFontSize(14);
        pdf.text(`Total Bet Amount: $${formattedTotalBetAmount}`, 10, startY + 10);

        // Imprimir y abrir el PDF
        pdf.autoPrint();
        window.open(pdf.output('bloburl'), '_blank');
    }
});
