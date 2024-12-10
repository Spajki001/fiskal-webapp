<?php
include 'connection.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

// Fetch users for the dropdowns
$firms = $conn->query("SELECT id, Ime, Prezime FROM meni_korisnik WHERE Uloga = 'firma'");
$referents = $conn->query("SELECT id, Ime, Prezime FROM meni_korisnik WHERE Uloga = 'referent'");

?>
<!DOCTYPE html>
<html lang="hr" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="apple-touch-icon" sizes="180x180" href="src/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="src/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="src/favicon-16x16.png">
    <link rel="manifest" href="src/site.webmanifest">
    <title>FISPRO Knjigovodstvo</title>
    <link rel="stylesheet" href="firme.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    <script>
        var userRole = "<?php echo $_SESSION['uloga']; ?>";
    </script>
</head>
<body>
    <div class="container mt-5">
        <div class="jumbotron text-center">
            <h1 class="display-4">Pregled partnera</h1>
            <p class="lead mt-4">Dobrodošli <?php echo $_SESSION['ime'] . " " . $_SESSION['prezime']; ?>!</p>
            <?php if ($_SESSION['uloga'] == 'admin' || $_SESSION['uloga'] == 'referent') { ?>
                <button class="btn btn-outline-primary mt-3 me-2" data-bs-toggle="modal" data-bs-target="#addUserModal"><i class="fa-solid fa-user-plus"></i> Dodaj korisnika</button>
                <button class="btn btn-outline-secondary mt-3 me-2" data-bs-toggle="modal" data-bs-target="#addFirmModal"><i class="fa-solid fa-building"></i> Dodaj partnera</button>
            <?php } ?>
            <?php if ($_SESSION['uloga'] == 'admin') { ?>
                <button class="btn btn-outline-info mt-3 me-2" id="showReferents"><i class="fa-solid fa-users"></i> Prikaži referente</button>
            <?php } ?>
            <button class="btn btn-outline-primary mt-3 me-2" id="filtrirajPartnereBtn" data-bs-toggle="modal" data-bs-target="#filterModal"><i class="fa-solid fa-filter"></i> Filtriraj partnere</button>
            <a href="logout.php" class="btn btn-outline-danger mt-3"><i class="fa-solid fa-right-from-bracket"></i> Odjava</a>
        </div>
        <div class="row table-container">
            <?php
                $totalIznosNaknade = 0;
                if ($_SESSION['uloga'] == 'referent') {
                    $sql = "SELECT * FROM firme_podatci WHERE referent_id = $_SESSION[user_id]";
                    $result = $conn->query($sql);
                } else if ($_SESSION['uloga'] == 'firma') {
                    $sql = "SELECT * FROM firme_podatci WHERE firma_id = $_SESSION[user_id]";
                    $result = $conn->query($sql);
                } else if ($_SESSION['uloga'] == 'admin') {
                    $sql = "SELECT * FROM firme_podatci";
                    $result = $conn->query($sql);
                } else {
                    echo "<div class='jumbotron text-center'>
                    <h3 class='display-6 text-align-center'>Nemate pristup</h3>
                    </div>";
                }

                if ($result->num_rows > 0){
                    while ($row = $result->fetch_assoc()) {
                        $totalIznosNaknade += $row['Iznos_naknade'];
                    }
                }
            ?>
        </div>
        <div class="row mt-3">
            <div class="col-12 d-flex justify-content-center">
                <div class="input-group" style="width: 300px;">
                    <input type="text" id="searchBar" class="form-control rounded-start" placeholder="Pretraži naziv ili OIB">
                    <button class="btn btn-outline-secondary rounded-end" type="button" id="searchButton"><i class="fa-solid fa-search"></i></button>
                    <div id="searchSuggestions" class="list-group position-absolute w-100" style="margin-top: 40px;"></div>
                </div>
            </div>
        </div>
        <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive mt-3">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Naziv</th>
                            <th>Adresa</th>
                            <th>OIB</th>
                            <th>PDV</th>
                            <th>Iznos naknade</th>
                            <th>Vrsta knjigovodstva</th>
                            <th>Plaća</th>
                            <th>Drugi dohodak</th>
                            <th>Dodatne usluge</th>
                            <th>Fakturira</th>
                            <?php if ($_SESSION['uloga'] == 'admin' || $_SESSION['uloga'] == 'referent') { ?>
                                <th>Referent</th>
                                <th style="width: 150px;">Akcije</th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $result->data_seek(0); // Reset result pointer to the beginning
                            while ($row = $result->fetch_assoc()) {
                                if ($_SESSION['uloga'] == 'admin' || $_SESSION['uloga'] == 'firma') {
                                    $sql = "SELECT Prezime FROM meni_korisnik WHERE id = $row[referent_id]";
                                    $referent_result = $conn->query($sql);
                                    $row_ref = $referent_result->fetch_assoc();
                                }
                                echo "<tr>
                                        <td>" . $row['ID'] . "</td>
                                        <td>" . $row['Naziv'] . "</td>
                                        <td>" . $row['Adresa'] . "</td>
                                        <td>" . $row['OIB'] . "</td>
                                        <td>" . $row['PDV'] . "</td>
                                        <td>" . $row['Iznos_naknade'] . " €</td>
                                        <td>" . $row['Vrsta_knjigovodstva'] . "</td>
                                        <td>" . $row['Placa'] . "</td>
                                        <td>" . $row['Drugi_dohodak'] . "</td>
                                        <td>" . $row['Dodatne_usluge'] . "</td>
                                        <td>" . $row['Fakturira'] . "</td>";
                                        if ($_SESSION['uloga'] == 'admin' || $_SESSION['uloga'] == 'firma') {
                                            echo "<td>" . $row_ref['Prezime'] . "</td>";
                                        }
                                        if ($_SESSION['uloga'] == 'admin' || $_SESSION['uloga'] == 'referent') {
                                            echo "<td class='text-center'>
                                                    <div class='d-flex justify-content-center gap-2'>
                                                        <button class='btn btn-primary btn-sm edit-partner' data-id='" . $row['ID'] . "'><i class='fa-solid fa-pen-to-square'></i> Uredi</button>
                                                        <button class='btn btn-outline-danger btn-sm delete-partner' data-id='" . $row['ID'] . "'><i class='fa-solid fa-trash'></i> Ukloni</button>
                                                    </div>
                                                  </td>";
                                        }
                                    echo "</tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="row mt-3">
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <button id="exportButton" class="btn btn-success"><i class="fa-solid fa-file-excel"></i> Otvori u Excel-u</button>
                    <div class="jumbotron mb-0">
                        <h4 class="display-8 mt-3"><strong>Ukupno naknade: </strong><?php echo number_format($totalIznosNaknade, 2); ?> €</h4>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class='jumbotron text-center'>
                <h3 class='display-6 text-align-center'>Nema podataka</h3>
            </div>
        <?php endif; ?>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Dodaj korisnika</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addUserForm">
                        <div class="mb-3">
                            <label for="userIme" class="form-label">Ime</label>
                            <input type="text" class="form-control" id="userIme" name="ime" required>
                        </div>
                        <div class="mb-3">
                            <label for="userPrezime" class="form-label">Prezime</label>
                            <input type="text" class="form-control" id="userPrezime" name="prezime" required>
                        </div>
                        <div class="mb-3">
                            <label for="userOIB" class="form-label">OIB</label>
                            <input type="text" class="form-control" id="userOIB" name="oib" maxlength="11" required>
                        </div>
                        <div class="mb-3">
                            <label for="userUsername" class="form-label">Korisničko ime</label>
                            <input type="text" class="form-control" id="userUsername" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="userPassword" class="form-label">Zaporka</label>
                            <input type="password" class="form-control" id="userPassword" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="userUloga" class="form-label">Uloga</label>
                            <select class="form-select" id="userUloga" name="uloga" <?php echo ($_SESSION['uloga'] == 'referent') ? 'disabled' : ''; ?> required>
                                <option value="referent">Referent</option>
                                <option value="firma" <?php echo ($_SESSION['uloga'] == 'referent') ? 'selected' : ''; ?>>Firma</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="saveUser"><i class="fa-solid fa-floppy-disk"></i> Spremi promjene</button>
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Odustani</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Add Partner Modal -->
    <div class="modal fade" id="addFirmModal" tabindex="-1" aria-labelledby="addFirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addFirmModalLabel">Dodaj partnera</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addFirmForm">
                        <div class="mb-3">
                            <label for="firmNaziv" class="form-label">Naziv</label>
                            <input type="text" class="form-control" id="firmNaziv" name="naziv" required>
                        </div>
                        <div class="mb-3">
                            <label for="firmAdresa" class="form-label">Adresa</label>
                            <input type="text" class="form-control" id="firmAdresa" name="adresa" required>
                        </div>
                        <div class="mb-3">
                            <label for="firmOIB" class="form-label">OIB</label>
                            <input type="text" class="form-control" id="firmOIB" name="oib" maxlength="11" required>
                        </div>
                        <div class="mb-3">
                            <label for="firmPDV" class="form-label">PDV</label>
                            <select class="form-select" id="firmPDV" name="pdv" required>
                                <option value="da">Da</option>
                                <option value="ne">Ne</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="firmIznosNaknade" class="form-label">Iznos naknade</label>
                            <input type="text" class="form-control" id="firmIznosNaknade" name="iznos_naknade" required>
                        </div>
                        <div class="mb-3">
                            <label for="firmFirma" class="form-label">Partner</label>
                            <select class="form-select" id="firmFirma" name="firma_id" required>
                                <?php while ($firm = $firms->fetch_assoc()) { ?>
                                    <option value="<?php echo $firm['id']; ?>"><?php echo $firm['Ime'] . " " . $firm['Prezime']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="firmReferent" class="form-label">Referent</label>
                            <select class="form-select" id="firmReferent" name="referent_id" required>
                                <?php 
                                    $referents->data_seek(0); // Reset result pointer to the beginning
                                    while ($referent = $referents->fetch_assoc()) { 
                                ?>
                                    <option value="<?php echo $referent['id']; ?>"><?php echo $referent['Ime'] . " " . $referent['Prezime']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="firmVrstaKnjigovodstva" class="form-label">Vrsta knjigovodstva</label>
                            <select class="form-select" id="firmVrstaKnjigovodstva" name="vrsta_knjigovodstva" required>
                                <option value="jednostavno">Jednostavno</option>
                                <option value="dvojno">Dvojno</option>
                                <option value="paušal">Paušal</option>
                                <option value="proračun">Proračun</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="firmPlaca" class="form-label">Plaća</label>
                            <select class="form-select" id="firmPlaca" name="placa" required>
                                <option value="da">Da</option>
                                <option value="ne">Ne</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="firmDrugiDohodak" class="form-label">Drugi dohodak</label>
                            <select class="form-select" id="firmDrugiDohodak" name="drugi_dohodak" required>
                                <option value="da">Da</option>
                                <option value="ne">Ne</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="firmDodatneUsluge" class="form-label">Dodatne usluge</label>
                            <select class="form-select" id="firmDodatneUsluge" name="dodatne_usluge" required>
                                <option value="nema">Nema</option>
                                <option value="e-račun">E-račun</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="firmFakturira" class="form-label">Fakturira</label>
                            <select class="form-select" id="firmFakturira" name="fakturira" required>
                                <option value="Agencija Dast">Agencija Dast</option>
                                <option value="Agencija">Agencija</option>
                                <option value="Ad d.o.o.">Ad d.o.o.</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="saveFirm"><i class="fa-solid fa-floppy-disk"></i> Spremi promjene</button>
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Odustani</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Referents Modal -->
    <div class="modal fade" id="referentsModal" tabindex="-1" aria-labelledby="referentsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="referentsModalLabel">Referenti</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Ime</th>
                                <th>Prezime</th>
                                <th>OIB</th>
                                <th>Akcije</th>
                            </tr>
                        </thead>
                        <tbody id="referentsTableBody">
                            <!-- Referents will be loaded here via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit Referent Modal -->
    <div class="modal fade" id="editReferentModal" tabindex="-1" aria-labelledby="editReferentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editReferentModalLabel">Uredi referenta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editReferentForm">
                        <div class="mb-3">
                            <label for="editReferentIme" class="form-label">Ime</label>
                            <input type="text" class="form-control" id="editReferentIme" name="ime" required>
                        </div>
                        <div class="mb-3">
                            <label for="editReferentPrezime" class="form-label">Prezime</label>
                            <input type="text" class="form-control" id="editReferentPrezime" name="prezime" required>
                        </div>
                        <div class="mb-3">
                            <label for="editReferentOIB" class="form-label">OIB</label>
                            <input type="text" class="form-control" id="editReferentOIB" name="oib" maxlength="11" required>
                        </div>
                        <div class="mb-3">
                            <label for="editReferentUsername" class="form-label">Korisničko ime</label>
                            <input type="text" class="form-control" id="editReferentUsername" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="editReferentPassword" class="form-label">Zaporka</label>
                            <input type="password" class="form-control" id="editReferentPassword" name="password">
                        </div>
                        <div class="mb-3">
                            <label for="editReferentUloga" class="form-label">Uloga</label>
                            <select class="form-select" id="editReferentUloga" name="uloga" required readonly>
                                <option value="referent" selected>Referent</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="saveEditReferent"><i class="fa-solid fa-floppy-disk"></i> Spremi promjene</button>
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Odustani</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Filtriraj partnere</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm">
                        <div class="mb-3">
                            <label for="pdv" class="form-label">PDV</label>
                            <select class="form-select" id="pdv" name="pdv">
                                <option value="">--Select--</option>
                                <option value="da">Da</option>
                                <option value="ne">Ne</option>
                            </select>
                        </div>
                        <?php if ($_SESSION['uloga'] == 'admin') { ?>
                        <div class="mb-3">
                            <label for="referent" class="form-label">Referent</label>
                            <select class="form-select" id="referent" name="referent">
                                <option value="">--Select--</option>
                                <?php 
                                    $referents->data_seek(0); // Reset result pointer to the beginning
                                    while ($referentFilter = $referents->fetch_assoc()) { 
                                ?>
                                    <option value="<?php echo $referentFilter['id']; ?>"><?php echo $referentFilter['Ime'] . " " . $referentFilter['Prezime']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <?php } ?>
                        <div class="mb-3">
                            <label for="vrstaKnjigovodstva" class="form-label">Vrsta knjigovodstva</label>
                            <select class="form-select" id="vrstaKnjigovodstva" name="vrstaKnjigovodstva">
                                <option value="">--Select--</option>
                                <option value="jednostavno">Jednostavno</option>
                                <option value="dvojno">Dvojno</option>
                                <option value="paušal">Paušal</option>
                                <option value="proračun">Proračun</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="placa" class="form-label">Plaća</label>
                            <select class="form-select" id="placa" name="placa">
                                <option value="">--Select--</option>
                                <option value="da">Da</option>
                                <option value="ne">Ne</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="drugiDohodak" class="form-label">Drugi dohodak</label>
                            <select class="form-select" id="drugiDohodak" name="drugiDohodak">
                                <option value="">--Select--</option>
                                <option value="da">Da</option>
                                <option value="ne">Ne</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="dodatneUsluge" class="form-label">Dodatne usluge</label>
                            <select class="form-select" id="dodatneUsluge" name="dodatneUsluge">
                                <option value="">--Select--</option>
                                <option value="nema">Nema</option>
                                <option value="e-račun">E-račun</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="fakturira" class="form-label">Fakturira</label>
                            <select class="form-select" id="fakturira" name="fakturira">
                                <option value="">--Select--</option>
                                <option value="Agencija Dast">Agencija Dast</option>
                                <option value="Agencija">Agencija</option>
                                <option value="Ad d.o.o.">Ad d.o.o.</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit Partner Modal -->
    <div class="modal fade" id="editPartnerModal" tabindex="-1" aria-labelledby="editPartnerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPartnerModalLabel">Uredi partnera</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editPartnerForm">
                        <div class="mb-3">
                            <label for="editPartnerNaziv" class="form-label">Naziv</label>
                            <input type="text" class="form-control" id="editPartnerNaziv" name="naziv" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPartnerAdresa" class="form-label">Adresa</label>
                            <input type="text" class="form-control" id="editPartnerAdresa" name="adresa" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPartnerOIB" class="form-label">OIB</label>
                            <input type="text" class="form-control" id="editPartnerOIB" name="oib" maxlength="11" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPartnerPDV" class="form-label">PDV</label>
                            <select class="form-select" id="editPartnerPDV" name="pdv" required>
                                <option value="da">Da</option>
                                <option value="ne">Ne</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editPartnerIznosNaknade" class="form-label">Iznos naknade</label>
                            <input type="text" class="form-control" id="editPartnerIznosNaknade" name="iznos_naknade" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPartnerFirma" class="form-label">Partner</label>
                            <select class="form-select" id="editPartnerFirma" name="firma_id" required>
                                <?php 
                                    $firms->data_seek(0); // Reset result pointer to the beginning
                                    while ($firm = $firms->fetch_assoc()) { 
                                ?>
                                    <option value="<?php echo $firm['id']; ?>"><?php echo $firm['Ime'] . " " . $firm['Prezime']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editPartnerReferent" class="form-label">Referent</label>
                            <select class="form-select" id="editPartnerReferent" name="referent_id" required>
                                <?php
                                    $referents->data_seek(0); // Reset result pointer to the beginning 
                                    while ($referent = $referents->fetch_assoc()) { 
                                ?>
                                    <option value="<?php echo $referent['id']; ?>"><?php echo $referent['Ime'] . " " . $referent['Prezime']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editPartnerVrstaKnjigovodstva" class="form-label">Vrsta knjigovodstva</label>
                            <select class="form-select" id="editPartnerVrstaKnjigovodstva" name="vrsta_knjigovodstva" required>
                                <option value="jednostavno">Jednostavno</option>
                                <option value="dvojno">Dvojno</option>
                                <option value="paušal">Paušal</option>
                                <option value="proračun">Proračun</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editPartnerPlaca" class="form-label">Plaća</label>
                            <select class="form-select" id="editPartnerPlaca" name="placa" required>
                                <option value="da">Da</option>
                                <option value="ne">Ne</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editPartnerDrugiDohodak" class="form-label">Drugi dohodak</label>
                            <select class="form-select" id="editPartnerDrugiDohodak" name="drugi_dohodak" required>
                                <option value="da">Da</option>
                                <option value="ne">Ne</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editPartnerDodatneUsluge" class="form-label">Dodatne usluge</label>
                            <select class="form-select" id="editPartnerDodatneUsluge" name="dodatne_usluge" required>
                                <option value="nema">Nema</option>
                                <option value="e-račun">E-račun</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editPartnerFakturira" class="form-label">Fakturira</label>
                            <select class="form-select" id="editPartnerFakturira" name="fakturira" required>
                                <option value="Agencija Dast">Agencija Dast</option>
                                <option value="Agencija">Agencija</option>
                                <option value="AD d.o.o.">AD d.o.o.</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="saveEditPartner"><i class="fa-solid fa-floppy-disk"></i> Spremi promjene</button>
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Odustani</button>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <div>V1.0-beta</div>
        <div>&copy; 2024 Fiskal d.o.o.</div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="firme.js?v=1.0"></script>
</body>
</html>