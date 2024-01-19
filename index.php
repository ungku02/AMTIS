<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electricity Tariff Calculator</title>
    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
        background-color: black;
    }

    .container {
        background-color: #ffffff;
        border-radius: 10px;
        padding: 20px;
        margin-top: 20px;
        /* Update box-shadow for the green neon effect */
        box-shadow: 0 0 20px rgba(40, 167, 69, 0.5), 0 0 40px rgba(40, 167, 69, 0.5), 0 0 80px rgba(40, 167, 69, 0.5);
    }

    .btn-primary {
        background-color: #28a745;
        border-color: #28a745;
        box-shadow: 0 0 10px rgba(40, 167, 69, 0.5);
    }

    .btn-primary:hover {
        background-color: #218838;
        border-color: #218838;
        box-shadow: 0 0 15px rgba(33, 136, 56, 0.8);
    }

        .result-card {
            margin-top: 20px;
            border: 1px solid #28a745;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(40, 167, 69, 0.5), 0 0 20px rgba(40, 167, 69, 0.3) inset;
        }

        .card-title {
            color: black;
        }

        .text-danger {
            color: #dc3545;
        }

        table {
            margin-top: 20px;
        }
    </style>

</head>
<body>

<div class="container">
    <h2 class="mb-4">Electricity Tariff Calculator</h2>
    <form method="post" action="">
        <div class="form-group">
            <label for="voltage">Voltage (V):</label>
            <input type="number" class="form-control" name="voltage" step="0.01" required>
        </div>

        <div class="form-group">
            <label for="current">Current (A):</label>
            <input type="number" class="form-control" name="current" step="0.01" required>
        </div>

        <div class="form-group">
            <label for="rate">Current Rate (sen/kWh):</label>
            <input type="number" class="form-control" name="rate" step="0.01" required>
        </div>

        <button type="submit" class="btn btn-primary">Calculate</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve user input
        $voltage = $_POST["voltage"];
        $current = $_POST["current"];
        $rate = $_POST["rate"];

        // Validate inputs 
        if (!empty($voltage) && !empty($current) && !empty($rate)) {
            // Calculate power and energy
            $power = $voltage * $current;
            $energy = $power / 1000; // Assuming 1 hour for simplicity

            // Calculate total charge based on tariff
            $totalCharge = calculateElectricityCharge($energy, $rate/100);

            // Display results 
            echo "<div class='card result-card'>";
            echo "<div class='card-body'>";
            echo "<h3 class='card-title text-center'>Electricity Consumption Summary</h3>";
            echo "<p class='card-text'>Power: $power watts</p>";
            echo "<p class='card-text'>Energy: " . number_format($energy, 5) . " kWh</p>";
            echo "<p class='card-text'>Total Charge: RM $totalCharge</p>";
            echo "</div>";
            echo "</div>";

             // Display the reference table with collapse effect
             echo '<h3 class="mt-4 text-center card result-card card-title" data-toggle="collapse" href="#hourlyReference">Hourly Consumption Reference</h3>';
             echo '<div id="hourlyReference" class="collapse">';
             echo '<table class="table table-bordered table-hover">';
             echo '<thead class="thead-light">';
             echo '<tr>';
             echo '<th scope="col">Hour</th>';
             echo '<th scope="col">Energy (kWh)</th>';
             echo '<th scope="col">Total (RM)</th>';
             echo '</tr>';
             echo '</thead>';
             echo '<tbody>';
 
             // Display hourly data 
             for ($hour = 1; $hour <= 24; $hour++) {
                 $hourlyEnergy = $energy * $hour;
                 $hourlyTotalCharge = calculateElectricityCharge($hourlyEnergy, $rate / 100);
 
                 echo '<tr>';
                 echo '<td>' . $hour . '</td>';
                 echo '<td>' . number_format($hourlyEnergy, 5) . '</td>';
                 echo '<td>' . number_format($hourlyTotalCharge, 2) . '</td>';
                 echo '</tr>';
             }
 
             echo '</tbody>';
             echo '</table>';
             echo '</div>';
         
        } else {
            echo "<p class='text-danger mt-4 text-center'>Please fill in all the fields.</p>";
        }
    }

    function calculateElectricityCharge($energy, $rate) {
        // Tariff rates based on TNB residential tariff
        $rates = [
            ['limit' => 200, 'rate' => 21.80],
            ['limit' => 300, 'rate' => 33.40],
            ['limit' => 600, 'rate' => 51.60],
            ['limit' => 900, 'rate' => 54.60],
            ['limit' => PHP_INT_MAX, 'rate' => 57.10],
        ];

      
        $totalCharge = 0;

        // Calculate total charge based on tariff
        foreach ($rates as $tier) {
            if ($energy > 0) {
                $consumed = min($energy, $tier['limit']);
                $totalCharge += ($consumed * $rate);
                $energy -= $consumed;
            } else {
                break;
            }
        }

        return number_format($totalCharge, 2);
    }
    ?>
</div>

<!-- Bootstrap 4 JS and Popper.js (for dropdowns) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>

</body>
</html>
