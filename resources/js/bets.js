import { jsPDF } from 'jspdf';

document.addEventListener('DOMContentLoaded', () => {
    class BetForm {
        constructor() {
            this.sellerMargin = 0;
            this.totalBetAmountInput = document.getElementById('total_bet_amount');
            this.sellerMarginInput = document.getElementById('seller_margin');
            this.sellerMarginValueInput = document.getElementById('seller_margin_value');
            this.betRowsContainer = document.getElementById('betRows');
            this.addRowButton = document.getElementById('addRow');
            this.form = document.getElementById('betForm');
            this.drawNumberSelect = document.getElementById('draw_number');

            this.addEventListeners();
        }

        addEventListeners() {
            // Escuchar el cambio del número de sorteo
            this.drawNumberSelect.addEventListener('change', (event) => this.handleDrawNumberChange(event));

            // Escuchar el cambio de los montos de las apuestas
            this.betRowsContainer.addEventListener('input', (event) => {
                if (event.target.classList.contains('bet-amount')) {
                    this.updateTotals();
                }
            });

            // Escuchar el click para agregar fila de apuestas
            this.addRowButton.addEventListener('click', () => this.addBetRow());

            // Escuchar el click para eliminar una fila de apuestas
            this.betRowsContainer.addEventListener('click', (event) => {
                if (event.target.classList.contains('remove-row')) {
                    this.removeBetRow(event.target);
                }
            });

            // Escuchar el submit del formulario
            this.form.addEventListener('submit', (event) => this.handleSubmit(event));

            // Escuchar el click para generar el PDF de detalles de la apuesta
            document.getElementById('printBetDetails').addEventListener('click', () => this.printBetDetails());
        }

        // Maneja el cambio del número de sorteo
        handleDrawNumberChange(event) {
            const selectedOption = this.drawNumberSelect.options[this.drawNumberSelect.selectedIndex];
            const sellerMargin = selectedOption.getAttribute('data-seller-margin');
            
            // Restablecer el campo de seller margin
            this.sellerMarginInput.value = sellerMargin ? sellerMargin : '';
            this.sellerMargin = sellerMargin ? parseFloat(sellerMargin) : 0;

            // Reiniciar las apuestas y recalcular
            this.updateTotals();
        }

        // Añadir una nueva fila para la apuesta
        addBetRow() {
            const newRow = document.createElement('div');
            newRow.classList.add('bet-row', 'mb-3');
            newRow.innerHTML = `
                <div class="input-group">
                    <input type="number" class="form-control" name="bet_number[]" min="1" placeholder="Enter Bet Number" required>
                    <input type="number" class="form-control bet-amount" name="bet_amount[]" min="2" placeholder="Enter Bet Amount" required>
                    <button type="button" class="btn btn-danger remove-row" style="margin-left: 5px;">-</button>
                </div>
            `;
            this.betRowsContainer.appendChild(newRow);
            this.updateTotals();
        }

        // Eliminar una fila de apuesta
        removeBetRow(button) {
            button.closest('.bet-row').remove();
            this.updateTotals();
        }

        // Actualizar el total y los márgenes
        updateTotals() {
            let totalBetAmount = 0;

            // Sumar los valores de las apuestas
            this.betRowsContainer.querySelectorAll('.bet-amount').forEach(input => {
                totalBetAmount += parseFloat(input.value) || 0;
            });

            // Actualiza el valor del margen del vendedor
            this.updateSellerMarginValue(totalBetAmount);
        }

        // Actualiza el valor del margen del vendedor basado en el total de las apuestas y el margen
        updateSellerMarginValue(totalBetAmount = 0) {
            // Obtener el valor del total de la apuesta
            totalBetAmount = totalBetAmount || parseFloat(this.totalBetAmountInput.value) || 0;

            // Calcular el valor del margen del vendedor
            const sellerMarginValue = (totalBetAmount * this.sellerMargin) / 100;

            // Actualizar el campo de valor de margen
            const formatter = new Intl.NumberFormat('de-DE');
            this.sellerMarginValueInput.value = formatter.format(Math.floor(sellerMarginValue));

            // Actualizar el total de la apuesta
            this.totalBetAmountInput.value = formatter.format(Math.floor(totalBetAmount));
        }

        // Maneja el submit del formulario
        handleSubmit(event) {
            event.preventDefault();
        
            const formData = new FormData(this.form);
        
            fetch(this.form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(async response => {
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.error || 'Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    this.printBetDetails(data.data);
                    Swal.fire({
                        title: 'Success',
                        text: data.success,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                    this.form.reset();
                } else if (data.errors) {
                    Swal.fire({
                        title: 'Error',
                        text: Object.values(data.errors).flat().join(', '),
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                } else if (data.error) {
                    Swal.fire({
                        title: 'Error',
                        text: data.error,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: error.message || 'Hubo un problema inesperado. Inténtelo de nuevo.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        }

        // Genera el PDF con los detalles de la apuesta
        printBetDetails(data) {
            const pdf = new jsPDF();
            
            pdf.setFontSize(16);
            pdf.text("Betting details", 10, 10);
            
            pdf.setFontSize(12);
            pdf.text(`Bet ID: ${data[0].bet_id}`, 160, 10);
        
            const headers = ["Draw Number", "Bet Number", "Bet Amount"];
            let startY = 30;
            const rowHeight = 10;
        
            headers.forEach((header, index) => {
                pdf.text(header, 10 + (index * 60), startY);
            });
        
            startY += rowHeight;
        
            let totalBetAmount = 0;
        
            data.forEach(bet => {
                const betEntries = Object.entries(bet.bet_number_and_bet_amount);
                pdf.text(bet.draw_number, 10, startY);
        
                betEntries.forEach(([betNumber, betAmount], index) => {
                    if (index === 0) {
                        pdf.text(betNumber, 70, startY);
                        pdf.text(betAmount, 130, startY);
                    } else {
                        startY += rowHeight;
                        pdf.text(betNumber, 70, startY);
                        pdf.text(betAmount, 130, startY);
                    }
                    totalBetAmount += parseFloat(betAmount);
                });
        
                startY += rowHeight;
            });
        
            const formattedDateTime = data[0].bet_date_time.replace("T", " ").substring(0, 19);
        
            pdf.text(`Bet Date/Time: ${formattedDateTime}`, 10, pdf.internal.pageSize.height - 20);
        
            const formattedTotalBetAmount = totalBetAmount.toLocaleString("es-CO", { minimumFractionDigits: 0 });
            pdf.setFontSize(14);
            pdf.text(`Total Bet Amount: ${formattedTotalBetAmount}`, 10, startY + 10);
        
            pdf.autoPrint();
            window.open(pdf.output('bloburl'), '_blank');
        }
    }

    new BetForm();
});
