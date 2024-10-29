<?php
include 'connection.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

$kasaDB = $_SESSION['kasaDB'];
$activeYear = $_SESSION['activeYear'];
$currentMonth = date('n');

// Fetch data for the chart
$sql = "USE $kasaDB";
$conn->query($sql);
$sql = "SELECT MONTH(Datum_KD) as month, SUM(Ukupno_KD) as total_income 
        FROM zakljucak_blagajne 
        WHERE YEAR(Datum_KD) = $activeYear 
        GROUP BY MONTH(Datum_KD)";
$result = $conn->query($sql);

$chartData = array_fill(1, 12, 0);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $chartData[$row['month']] = $row['total_income'];
    }
}

// Fetch data for the top 10 sold articles for the current month
$sql = "SELECT ma.Naziv_ART, COUNT(*) AS QuantitySold
        FROM skladiste_kartica sk
        INNER JOIN maloprodaja_artikli ma ON sk.SIFRA_ART = ma.SIFRA_ART
        WHERE MONTH(sk.Datum_KA) = $currentMonth AND sk.IdxUI = 1
        GROUP BY ma.Naziv_ART
        ORDER BY QuantitySold DESC
        LIMIT 10";
$result = $conn->query($sql);

$topArticlesData = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $topArticlesData[] = $row;
    }
}
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
    <title>FISPRO Analytics</title>
    <link rel="stylesheet" href="zakljucci.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container mt-5">
        <div class="jumbotron text-center">
            <h1 class="display-4">Pregled zaključaka</h1>
            <a href="pregled_stolova.php" class="btn btn-primary mt-3 me-2"><i class="fa-solid fa-receipt"></i> Pregled stolova</a>
            <a href="logout.php" class="btn btn-outline-danger mt-3"><i class="fa-solid fa-right-from-bracket"></i> Odjava</a>
        </div>
        <div class="row mt-4">
            <div class="col-12">
                <canvas id="incomeChart" style="max-height: 400px; width: 100%;"></canvas>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="mb-3">
                        <label for="monthFilter" class="form-label">Mjesec:</label>
                        <select id="monthFilter" class="form-select">
                            <option value="1">Siječanj</option>
                            <option value="2">Veljača</option>
                            <option value="3">Ožujak</option>
                            <option value="4">Travanj</option>
                            <option value="5">Svibanj</option>
                            <option value="6">Lipanj</option>
                            <option value="7">Srpanj</option>
                            <option value="8">Kolovoz</option>
                            <option value="9">Rujan</option>
                            <option value="10">Listopad</option>
                            <option value="11">Studeni</option>
                            <option value="12">Prosinac</option>
                        </select>
                    </div>
                </div>
                <canvas id="topArticlesChart" style="max-height: 400px; width: 100%;"></canvas>
            </div>
        </div>
        <div class="row table-container mt-5">
            <?php
                $sql = "SELECT * FROM zakljucak_blagajne";
                $result = $conn->query($sql);

                echo "<div class='table-responsive mt-3'>";
                echo "<table class='table table-striped table-hover'>";
                echo "<thead><tr>
                        <th>ID_KD</th>
                        <th>Računi</th>
                        <th>Datum_KD</th>
                        <th>ID_SKLAD</th>
                        <th>RacOd_KD</th>
                        <th>RacDo_KD</th>
                        <th>Ukupno_KD</th>
                        <th>Porez_KD</th>
                        <th>Porez1_KD</th>
                    </tr></thead>";
                echo "<tbody>";
                if ($result->num_rows > 0){
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $row['ID_KD'] . "</td>
                                <td> <a
                                    name='pregledRacuna'
                                    id='pregledRacuna'
                                    class='btn btn-primary'
                                    href='pregled_racuna.php?ID_KD=" . $row['ID_KD'] . "'
                                    role='button'
                                    ><i class='fa-solid fa-receipt'></i> Pregled</a>
                                </td>
                                <td>" . $row['Datum_KD'] . "</td>
                                <td>" . $row['ID_SKLAD'] . "</td>
                                <td>" . $row['RacOd_KD'] . "</td>
                                <td>" . $row['RacDo_KD'] . "</td>
                                <td>" . $row['Ukupno_KD'] . " €</td>
                                <td>" . $row['Porez_KD'] . " €</td>
                                <td>" . $row['Porez1_KD'] . " €</td>
                            </tr>";
                    }
                    echo "</tbody></table></div>";
                } else {
                    echo "<div class='jumbotron text-center'>
                    <h3 class='display-6 text-align-center'>Nema podataka</h3>
                    </div>";
                    echo "</tbody></table></div>";
                }
            ?>
        </div>
    </div>
    <div class="footer">
        <div>V1.0-beta</div>
        <div>&copy; 2024 Fiskal d.o.o.</div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const incomeCtx = document.getElementById('incomeChart').getContext('2d');
            const topArticlesCtx = document.getElementById('topArticlesChart').getContext('2d');
            const chartData = <?php echo json_encode(array_values($chartData)); ?>;
            let topArticlesData = <?php echo json_encode($topArticlesData); ?>;
            const labels = ['Siječanj', 'Veljača', 'Ožujak', 'Travanj', 'Svibanj', 'Lipanj', 'Srpanj', 'Kolovoz', 'Rujan', 'Listopad', 'Studeni', 'Prosinac'];
            
            // Set the default selected month in the dropdown
            document.getElementById('monthFilter').value = <?php echo $currentMonth; ?>;

            const incomeChart = new Chart(incomeCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Ukupni prihod (€)',
                        data: chartData,
                        backgroundColor: '#0d6efd'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false // Remove the legend
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: false // Remove the y-axis label
                            }
                        }
                    }
                }
            });

            function shortenLabel(label, maxLength = 10) {
                return label.length > maxLength ? label.substring(0, maxLength) + '...' : label;
            }

            const topArticlesChart = new Chart(topArticlesCtx, {
                type: 'bar',
                data: {
                    labels: topArticlesData.map(item => shortenLabel(item.Naziv_ART)),
                    datasets: [{
                        label: 'Količina prodanih artikala',
                        data: topArticlesData.map(item => item.QuantitySold),
                        backgroundColor: '#0d6efd'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false // Remove the legend
                        },
                        tooltip: {
                            callbacks: {
                                title: function (context) {
                                    const index = context[0].dataIndex;
                                    return topArticlesData[index].Naziv_ART;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: false // Remove the y-axis label
                            }
                        }
                    }
                }
            });

            document.getElementById('monthFilter').addEventListener('change', updateTopArticlesChart);

            function updateTopArticlesChart() {
                const selectedMonth = document.getElementById('monthFilter').value;

                fetch(`get_top_articles.php?month=${selectedMonth}`)
                    .then(response => response.json())
                    .then(data => {
                        topArticlesData = data; // Update the topArticlesData with the new data
                        topArticlesChart.data.labels = data.map(item => shortenLabel(item.Naziv_ART));
                        topArticlesChart.data.datasets[0].data = data.map(item => item.QuantitySold);
                        topArticlesChart.update();
                    });
            }
        });
    </script>
</body>
</html>