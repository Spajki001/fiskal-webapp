function domReady(fn) {
    if (
        document.readyState === "complete" ||
        document.readyState === "interactive"
    ) {
        setTimeout(fn, 1000);
    } else {
        document.addEventListener("DOMContentLoaded", fn);
    }
}

domReady(function () {

    const scanResultModal = new bootstrap.Modal(document.getElementById('scan-result-modal'));
    const scanResultForm = document.getElementById('scan-result-form');

    function onScanSuccess(decodedText, decodedResult) {
        try {
            console.log('Decoded Text:', decodedText); // Log the decoded text
            console.log('Decoded Result:', decodedResult); // Log the decoded result

            // Ensure decodedText is properly formatted JSON
            const scannedData = JSON.parse(decodedText.replace(/,(\d{2})/g, '.$1'));

            // Parse data into separate variables
            const sifra = scannedData[0]["Sifra"];
            const katalog = scannedData[0]["Katalog"];
            const naziv = scannedData[0]["Naziv"];
            const mpc = scannedData[0]["MPC"];

            // Populate modal fields
            document.getElementById('sifraInput').value = sifra;
            document.getElementById('katalogInput').value = katalog;
            document.getElementById('nazivInput').value = naziv;
            document.getElementById('mpcInput').value = mpc;

            // Reset amount field
            document.getElementById('amountInput').value = "";

            // Show modal
            scanResultModal.show();

            // Stop scanning
            htmlscanner.clear();

            scanResultForm.addEventListener('submit', function(event) {
                event.preventDefault();

                const amount = document.getElementById('amountInput').value;
                const currentDate = new Date();
                const formattedDate = `${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}-${String(currentDate.getDate()).padStart(2, '0')} ${String(currentDate.getHours()).padStart(2, '0')}:${String(currentDate.getMinutes()).padStart(2, '0')}:${String(currentDate.getSeconds()).padStart(2, '0')}`;

                console.log(formattedDate);

                // Prepare the data to be sent
                const data = {
                    sifra: sifra,
                    amount: amount,
                    userId: userId,
                    currentDate: formattedDate,
                    idInventure: IDInventure
                };

                // Log the data being sent
                console.log('Data being sent:', data);

                // Send the data using fetch
                fetch('submit_scan.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    if (response.ok) {
                        return response.text();
                    } else {
                        throw new Error('Network response was not ok');
                    }
                })
                .then(responseText => {
                    // Handle successful submission (e.g., show a success message, update the UI)
                    console.log('Data submitted successfully:', responseText);

                    // Restart the QR scanner
                    htmlscanner.render(onScanSuccess);

                    // Display the submitted article info below the QR scanner
                    const submittedArticleInfo = document.createElement('div');
                    submittedArticleInfo.innerHTML = `
                        <strong>Naziv:</strong> ${naziv}<br>
                        <strong>Šifra:</strong> ${sifra}<br>
                        <strong>Količina:</strong> ${amount}<br>
                        <strong>KorUpis${userId}:</strong> ${formattedDate}<br>
                    `;
                    document.getElementById('my-qr-reader').appendChild(submittedArticleInfo);

                    // Close the modal
                    scanResultModal.hide();
                })
                .catch(error => {
                    // Handle errors (e.g., show an error message)
                    console.error('Error submitting data:', error);
                });
            }, { once: true }); // Ensure the event listener is added only once
        } catch (e) {
            console.error('Error parsing JSON:', e);
            console.error('Problematic JSON:', decodedText);
        }
    };

    let htmlscanner = new Html5QrcodeScanner(
        "my-qr-reader",
        { fps: 20, qrbox: 250 }
    );
    htmlscanner.render(onScanSuccess);

    // Reactivate QR scanner when the modal is closed
    document.querySelector('.btn-close').addEventListener('click', () => {
        htmlscanner.render(onScanSuccess);
    });

    document.querySelector('.btn-secondary').addEventListener('click', () => {
        htmlscanner.render(onScanSuccess);
    });
});