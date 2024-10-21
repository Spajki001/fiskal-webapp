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
                const currentDate = new Date().toISOString().slice(0, 19).replace('T', ' ');

                // Create an XMLHttpRequest object
                const xhr = new XMLHttpRequest();

                // Set the request method and URL
                xhr.open('POST', 'submit_scan.php', true);

                // Set the content type header
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                // Define the function to be called when the request is complete
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        // Handle successful submission (e.g., show a success message, update the UI)
                        console.log('Data submitted successfully:', xhr.responseText);

                        // Display the submitted article info below the QR scanner
                        const submittedArticleInfo = document.createElement('div');
                        submittedArticleInfo.innerHTML = `
                            <strong>Šifra:</strong> ${sifra}<br>
                            <strong>Količina:</strong> ${amount}<br>
                            <strong>KorUpis${userId}:</strong> ${currentDate}<br>
                        `;
                        document.getElementById('my-qr-reader').appendChild(submittedArticleInfo);

                        // Close the modal
                        scanResultModal.hide();
                    } else {
                        // Handle errors (e.g., show an error message)
                        console.error('Error submitting data:', xhr.status, xhr.statusText);
                    }
                };

                // Send the data to the server
                xhr.send(`sifra=${sifra}&amount=${amount}&userId=${userId}&currentDate=${currentDate}&idInventure=${IDInventure}`);
            });
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
});