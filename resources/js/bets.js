import html2canvas from 'html2canvas';

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
            this.placeBetsButton = document.getElementById('placeBetsButton');

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
        }

        handlePlaceBetsClick(process) {
            // Deshabilitar el botón
            this.placeBetsButton.disabled = (process == 'start') ? true : false;

            // Cambiar el texto del botón
            this.placeBetsButton.textContent = (process == 'start') ? "Processing..." : "Place Bets";
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
            this.handlePlaceBetsClick('start');
        
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
                    Swal.fire({
                        title: 'Success',
                        text: data.success,
                        icon: 'success',
                        confirmButtonText: 'OK',
                        timer: 2000,
                        willClose: () => {
                            this.showSuccessfulView(data.data);
                        }
                    });

                } else if (data.errors) {
                    this.handlePlaceBetsClick('end');
                    Swal.fire({
                        title: 'Error',
                        text: Object.values(data.errors).flat().join(', '),
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                } else if (data.error) {
                    this.handlePlaceBetsClick('end');
                    Swal.fire({
                        title: 'Error',
                        text: data.error,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                this.handlePlaceBetsClick('end');
                Swal.fire({
                    title: 'Error',
                    text: error.message || 'Hubo un problema inesperado. Inténtelo de nuevo.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        }

        printBetDetails() {
            const container = document.getElementById('successful-view');
        
            html2canvas(container).then(canvas => {
                const imgData = canvas.toDataURL('image/jpeg', 1.0);
                const link = document.createElement('a');
                link.href = imgData;
                link.download = 'bet_details.jpg';
                link.click();
            });
        }
        
        populateBetDetailsTable(betData) {
            const tableBody = document.getElementById('betDetailsTableBody');
            let totalAmount = 0;

            betData.forEach((bet) => {
                const row = document.createElement('tr');

                const betNumberCell = document.createElement('td');
                betNumberCell.textContent = Object.keys(bet.bet_number_and_bet_amount)[0];
                row.appendChild(betNumberCell);

                const betAmountCell = document.createElement('td');
                const betAmount = Number(Object.values(bet.bet_number_and_bet_amount)[0]);
                betAmountCell.textContent = betAmount.toLocaleString();
                row.appendChild(betAmountCell);

                tableBody.appendChild(row);

                totalAmount += betAmount;
            });

            document.getElementById('betId').textContent = betData[0].bet_id;
            document.getElementById('drawNumber').textContent = betData[0].draw_number;
            document.getElementById('betDateTime').textContent = betData[0].bet_date_time;
            document.getElementById('totalAmount').textContent = totalAmount.toLocaleString();
        }

        showSuccessfulView(betData) {
            document.getElementById('successful-view').classList.remove('d-none');
            document.getElementById('bet-view').classList.add('d-none');
            document.getElementById('navbar').classList.add('d-none');
            this.populateBetDetailsTable(betData);
            this.printBetDetails();
        };
    }

    new BetForm();
});
