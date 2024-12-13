$(document).ready(function() {
    // Test if jQuery is working
    console.log('jQuery is ready');

    // Event listener for the export button
    $('#exportButton').click(function() {
        console.log('Exporting data...');

        // Clone the table to manipulate it without affecting the original table
        var tableClone = $('table').clone();

        // Remove the action buttons column
        tableClone.find('th:last-child, td:last-child').remove();

        // Convert the table to a worksheet
        var wb = XLSX.utils.table_to_book(tableClone[0], {sheet: "Sheet JS"});

        // Ensure OIB column is treated as a string
        var ws = wb.Sheets["Sheet JS"];
        var range = XLSX.utils.decode_range(ws['!ref']);
        for (var R = range.s.r + 1; R <= range.e.r; ++R) {
            var cell_address = {c: 3, r: R}; // OIB column is the 4th column (index 3)
            var cell_ref = XLSX.utils.encode_cell(cell_address);
            if (ws[cell_ref] && ws[cell_ref].v) {
                ws[cell_ref].t = 's'; // Set cell type to string
                ws[cell_ref].v = ws[cell_ref].v.toString(); // Ensure value is a string
            }
        }

        // Adjust column widths to fit content
        var colWidths = [];
        for (var C = range.s.c; C <= range.e.c; ++C) {
            var maxWidth = 10; // Minimum column width
            for (var R = range.s.r; R <= range.e.r; ++R) {
                var cell_address = {c: C, r: R};
                var cell_ref = XLSX.utils.encode_cell(cell_address);
                var cell = ws[cell_ref];
                if (cell && cell.v) {
                    var cellValue = cell.v.toString();
                    maxWidth = Math.max(maxWidth, cellValue.length);
                }
            }
            colWidths.push({wch: maxWidth});
        }
        ws['!cols'] = colWidths;

        // Write the workbook and trigger the download
        XLSX.writeFile(wb, 'Pregled_partnera.xlsx');
        console.log('Data exported!');
    });

    // Event listener for the "Dodaj korisnika" button
    $('#saveUser').click(function() {
        const formData = new FormData($('#addUserForm')[0]);

        // Send AJAX request to add the user
        fetch('add_user.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Greška pri dodavanju korisnika: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Greška pri dodavanju korisnika:', error);
        });
    });

    // Event listener for the "Dodaj firmu" button
    $('#saveFirm').click(function() {
        const formData = new FormData($('#addFirmForm')[0]);

        // Send AJAX request to add the firm
        fetch('add_partner.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // After successfully adding a partner, save the partner history
                fetch('save_partner_history.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        console.error('Error saving partner history:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error saving partner history:', error);
                });

                location.reload();
            } else {
                alert('Greška pri dodavanju partnera: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Greška pri dodavanju partnera:', error);
        });
    });

    // Event listener for the "Prikaži referente" button
    $('#showReferents').click(function() {
        // Fetch referents data
        fetch('get_referents.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const referentsTableBody = $('#referentsTableBody');
                referentsTableBody.empty();
                data.referents.forEach(referent => {
                    referentsTableBody.append(`
                        <tr>
                            <td>${referent.Ime}</td>
                            <td>${referent.Prezime}</td>
                            <td>${referent.OIB}</td>
                            <td class="text-center">
                                <div class='d-flex justify-content-center gap-2'>
                                    <button class="btn btn-primary btn-sm edit-referent d-block" data-id="${referent.id}"><i class="fa-solid fa-pen-to-square"></i> Uredi</button>
                                    <button class="btn btn-outline-danger btn-sm delete-referent d-block" data-id="${referent.id}"><i class="fa-solid fa-trash"></i> Ukloni</button>
                                </div>
                            </td>
                        </tr>
                    `);
                });
                $('#referentsModal').modal('show');
            } else {
                alert('Greška pri dohvaćanju referenata: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Greška pri dohvaćanju referenata:', error);
        });
    });

    // Event listener for the "Uredi" button
    $(document).on('click', '.edit-referent', function() {
        const referentId = $(this).data('id');
        fetch(`get_referent.php?id=${referentId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#editReferentIme').val(data.referent.Ime);
                $('#editReferentPrezime').val(data.referent.Prezime);
                $('#editReferentOIB').val(data.referent.OIB);
                $('#editReferentUsername').val(data.referent.Username);
                $('#editReferentPassword').val(data.referent.Password);
                $('#editReferentUloga').val(data.referent.Uloga);
                $('#saveEditReferent').data('id', referentId);
                $('#editReferentModal').modal('show');
                $('#referentsModal').modal('hide');
            } else {
                alert('Greška pri dohvaćanju referenta: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Greška pri dohvaćanju referenta:', error);
        });
    });

    // Event listener for the "Save changes" button in the edit modal
    $('#saveEditReferent').click(function() {
        const referentId = $(this).data('id');
        const formData = new FormData($('#editReferentForm')[0]);
        formData.append('id', referentId);

        // Send AJAX request to update the referent
        fetch('update_referent.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Greška pri uređivanju referenta: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Greška pri uređivanju referenta:', error);
        });
    });

    // Event listener for the "Ukloni" button
    $(document).on('click', '.delete-referent', function() {
        const referentId = $(this).data('id');
        if (confirm('Jeste li sigurni da želite ukloniti referenta? Uklanjanje referenta će obrisati i sve partnere dodjeljene tom referentu.')) {
            fetch(`delete_referent.php?id=${referentId}`, {
                method: 'GET'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Greška pri brisanju referenata: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Greška pri brisanju referenta:', error);
                alert('Greška pri brisanju referenta: ' + error.message);
            });
        }
    });

    // Ensure modals stack correctly
    $(document).on('show.bs.modal', '.modal', function () {
        var zIndex = 1040 + (10 * $('.modal:visible').length);
        $(this).css('z-index', zIndex);
        setTimeout(function() {
            $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
        }, 0);
    });

    // Remove the event listener for when the edit referent modal is hidden
    $('#editReferentModal').off('hidden.bs.modal');

    // Event listener for the "Filtriraj partnere" button
    $('#filtrirajPartnereBtn').click(function() {
        $('#filterModal').modal('show');
    });

    // Event listener for the filter form submission
    $('#filterForm').submit(function(event) {
        event.preventDefault();
        const filters = {
            pdv: $('#pdv').val(),
            referent: $('#referent').val(),
            vrstaKnjigovodstva: $('#vrstaKnjigovodstva').val(),
            placa: $('#placa').val(),
            drugiDohodak: $('#drugiDohodak').val(),
            dodatneUsluge: $('#dodatneUsluge').val(),
            fakturira: $('#fakturira').val()
        };

        // Send filters to the server
        fetch('filter_partners.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(filters)
        })
        .then(response => response.json())
        .then(data => {
            // Handle the filtered data
            updateTable(data);
            $('#filterModal').modal('hide');
        })
        .catch(error => {
            console.error('Greška pri filtriranju partnera:', error);
        });
    });

    // Event listener for the "Uredi" button for partners
    $(document).on('click', '.edit-partner', function() {
        const partnerId = $(this).data('id');
        fetch(`get_partner.php?id=${partnerId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#editPartnerNaziv').val(data.partner.Naziv);
                $('#editPartnerAdresa').val(data.partner.Adresa);
                $('#editPartnerOIB').val(data.partner.OIB);
                $('#editPartnerPDV').val(data.partner.PDV);
                $('#editPartnerIznosNaknade').val(data.partner.Iznos_naknade);
                $('#editPartnerFirma').val(data.partner.firma_id);
                $('#editPartnerReferent').val(data.partner.referent_id);
                $('#editPartnerVrstaKnjigovodstva').val(data.partner.Vrsta_knjigovodstva);
                $('#editPartnerPlaca').val(data.partner.Placa);
                $('#editPartnerDrugiDohodak').val(data.partner.Drugi_dohodak);
                $('#editPartnerDodatneUsluge').val(data.partner.Dodatne_usluge);
                $('#editPartnerFakturira').val(data.partner.Fakturira);
                $('#saveEditPartner').data('id', partnerId);
                $('#editPartnerModal').modal('show');
            } else {
                alert('Greška pri dohvaćanju partnera: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Greška pri dohvaćanju partnera:', error);
        });
    });

    // Event listener for the "Save changes" button in the edit partner modal
    $('#saveEditPartner').click(function() {
        const partnerId = $(this).data('id');
        const formData = new FormData($('#editPartnerForm')[0]);
        formData.append('id', partnerId);

        // Send AJAX request to update the partner
        fetch('update_partner.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // After successfully updating the partner, save the partner history
                fetch('save_partner_history.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        console.error('Error saving partner history:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error saving partner history:', error);
                });

                location.reload();
            } else {
                alert('Greška pri uređivanju partnera: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Greška pri uređivanju partnera:', error);
        });
    });

    // Event listener for the "Povijest promjena" button
    $('#historyPartner').click(function() {
        const partnerId = $('#saveEditPartner').data('id');

        // Close the edit partner modal
        $('#editPartnerModal').modal('hide');

        // Fetch partner history
        fetch(`get_partner_history.php?partner_id=${partnerId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const partnerHistoryTableBody = $('#partnerHistoryTableBody');
                partnerHistoryTableBody.empty();
                data.history.forEach(entry => {
                    partnerHistoryTableBody.append(`
                        <tr>
                            <td>${entry.Naziv}</td>
                            <td>${entry.Adresa}</td>
                            <td>${entry.OIB}</td>
                            <td>${entry.PDV}</td>
                            <td>${entry.Iznos_naknade}</td>
                            <td>${entry.Vrsta_knjigovodstva}</td>
                            <td>${entry.Placa}</td>
                            <td>${entry.Drugi_dohodak}</td>
                            <td>${entry.Dodatne_usluge}</td>
                            <td>${entry.Fakturira}</td>
                            <td>${entry.Datum_promjene}</td>
                        </tr>
                    `);
                });
                // Show the partner history modal
                $('#partnerHistoryModal').modal('show');
            } else {
                alert('Greška pri dohvaćanju povijesti partnera: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Greška pri dohvaćanju povijesti partnera:', error);
        });
    });

    // Event listener for the "Ukloni" button for partners
    $(document).on('click', '.delete-partner', function() {
        const partnerId = $(this).data('id');
        if (confirm('Jeste li sigurni da želite ukloniti partnera?')) {
            fetch(`delete_partner.php?id=${partnerId}`, {
                method: 'GET'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Greška pri brisanju partnera: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Greška pri brisanju partnera:', error);
                alert('Greška pri brisanju partnera: ' + error.message);
            });
        }
    });

    function updateTable(data) {
        const tableBody = $('table tbody');
        tableBody.empty();
        data.forEach(row => {
            tableBody.append(`
                <tr>
                    <td>${row.Naziv}</td>
                    <td>${row.Adresa}</td>
                    <td>${row.OIB}</td>
                    <td>${row.PDV}</td>
                    <td>${row.Iznos_naknade} €</td>
                    <td>${row.Vrsta_knjigovodstva}</td>
                    <td>${row.Placa}</td>
                    <td>${row.Drugi_dohodak}</td>
                    <td>${row.Dodatne_usluge}</td>
                    <td>${row.Fakturira}</td>
                    ${row.Referent ? `<td>${row.Referent}</td>` : ''}
                    ${(userRole === 'admin' || userRole === 'referent') ? `
                        <td class="text-center">
                            <div class="d-flex justify-content-center">
                                <button class="btn btn-primary btn-sm edit-partner me-2 mb-2" data-id="${row.ID}"><i class="fa-solid fa-pen-to-square"></i> Uredi</button>
                                <button class="btn btn-outline-danger btn-sm delete-partner mb-2" data-id="${row.ID}"><i class="fa-solid fa-trash"></i> Ukloni</button>
                            </div>
                        </td>
                    ` : ''}
                </tr>
            `);
        });
    }

    // Search functionality
    $('#searchBar').on('input', function() {
        let query = $(this).val().toLowerCase();
        if (query.length > 0) {
            $.ajax({
                url: 'search.php',
                method: 'GET',
                data: { query: query },
                success: function(data) {
                    $('#searchSuggestions').html(data);
                }
            });
        } else {
            $('#searchSuggestions').empty();
        }
    });

    $('#searchBar').on('keypress', function(e) {
        if (e.which == 13) { // Enter key pressed
            let query = $(this).val().toLowerCase();
            filterTable(query);
        }
    });

    $('#searchButton').click(function() {
        $('#searchBar').trigger($.Event('keypress', { which: 13 }));
    });

    $(document).on('click', '.search-suggestion', function(e) {
        e.preventDefault();
        let suggestionText = $(this).text();
        let oib = suggestionText.match(/\(([^)]+)\)/)[1]; // Extract OIB from the suggestion text
        $('#searchBar').val(suggestionText);
        $('#searchSuggestions').empty();
        filterTableByOIB(oib);
    });

    function filterTable(query) {
        $('table tbody tr').each(function() {
            let name = $(this).find('td:nth-child(1)').text().toLowerCase(); // Adjusted to match the correct column index
            let oib = $(this).find('td:nth-child(3)').text().toLowerCase(); // Adjusted to match the correct column index
            if (name.includes(query) || oib.includes(query)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    function filterTableByOIB(oib) {
        $('table tbody tr').each(function() {
            let rowOIB = $(this).find('td:nth-child(3)').text().toLowerCase(); // Adjusted to match the correct column index
            if (rowOIB === oib.toLowerCase()) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    // Close suggestions when clicking outside
    $(document).click(function(event) {
        if (!$(event.target).closest('#searchBar, #searchSuggestions').length) {
            $('#searchSuggestions').empty();
        }
    });
});