import { jsPDF } from "jspdf";

document.getElementById('printButton').addEventListener('click', function() {
    const doc = new jsPDF();

    const content = document.querySelector('.table');
    
    const totalBets = parseFloat(document.getElementById('totalBets').value);
    const totalSellerMargin = parseFloat(document.getElementById('totalSellerMargin').value);
    const totalHouseMargin = parseFloat(document.getElementById('totalHouseMargin').value);
    
    const formatNumber = (num) => {
        return num.toLocaleString('es-CO');
    };

    const formattedTotalBets = formatNumber(totalBets);
    const formattedTotalSellerMargin = formatNumber(totalSellerMargin);
    const formattedTotalHouseMargin = formatNumber(totalHouseMargin);
    
    const today = new Date();
    const dateString = today.toLocaleDateString();

    doc.text(`Date: ${dateString}`, 10, 10);

    doc.text(`Total Bets of the Day: ${formattedTotalBets}`, 10, 20);
    doc.text(`Total Seller Margin: ${formattedTotalSellerMargin}`, 10, 30);
    doc.text(`Total House Margin: ${formattedTotalHouseMargin}`, 10, 40);

    doc.html(content, {
        callback: function (doc) {
            doc.autoPrint();
            window.open(doc.output('bloburl'), '_blank');
        },
        margin: [0, 10, 10, 10], // Márgenes: dejamos más espacio en la parte superior para los totales
        x: 10, // Posición en el eje X
        y: 50, // Ajustamos la posición Y para que la tabla no se solape con los totales
        width: 180, // Ajustar el ancho de la tabla para que no se salga de los márgenes
        windowWidth: 800 // Ajustar el ancho de la ventana de visualización
    });
});
