<?php 
session_start();

if (isset($_POST['reset'])) {
    session_unset();
    session_destroy();
    session_start();
    $_SESSION['transactions'] = [];
}

if (!isset($_SESSION['transactions'])) {
    $_SESSION['transactions'] = [];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete'])) {
        $deleteIndex = $_POST['delete'];
        unset($_SESSION['transactions'][$deleteIndex]);
        $_SESSION['transactions'] = array_values($_SESSION['transactions']); // Reindex array
    } else if (isset($_POST['description']) && isset($_POST['amount'])) {
        $description = $_POST['description'];
        $amount = $_POST['amount'];
        $_SESSION['transactions'][] = ['description' => $description, 'amount' => $amount];
    } else if (isset($_POST['allowance']) && ($_SESSION['allowance'] ?? 0) == 0) {
        $_SESSION['allowance'] = $_POST['allowance'];
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$total = 0;
$allowance = $_SESSION['allowance'] ?? 0;
foreach ($_SESSION['transactions'] as $transaction) {
    $total += $transaction['amount'];
}
$remaining = $allowance - $total;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget Tracker</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; }
        .container { width: 100%; margin: 0 auto; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 10px; text-align: left; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-5"><b>Budget Tracker</b></h1>

        <div class="row">
            
            <div class="col-lg-4">
                <div class="row">
                    
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body" style="border: 1px solid">
                                <h3 class="card-title" style="font-size: 25px;"><b>Set Monthly Allowance</b></h3>
                                <form method="POST" action="" class="mb-3">
                                    <div class="row">
                                        <div class="col-md-12 mt-3">
                                            <div class="form-group">
                                                <input type="number" id="allowance" name="allowance" class="form-control" placeholder="Monthly Allowance" required <?php echo ($allowance != 0) ? 'disabled' : ''; ?>>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-success" <?php echo ($allowance != 0) ? 'disabled' : ''; ?>>Add</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-12 mt-3">
                        <div class="card">
                            <div class="card-body" style="border: 1px solid">
                                <h3 class="card-title" style="font-size: 25px;"><b>Set Monthly Expenses</b></h3>
                                <form method="POST" action="" class="mb-3">
                                    <div class="row mt-3">

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <input type="text" id="description" name="description" placeholder="Expenses Description" class="form-control" required <?php echo ($allowance == 0) ? 'disabled' : ''; ?>>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <input type="number" id="amount" name="amount" placeholder="Expenses Amount" class="form-control" required <?php echo ($allowance == 0) ? 'disabled' : ''; ?>>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-success" <?php echo ($allowance == 0) ? 'disabled' : ''; ?>>Add</button>
                                        </div>

                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 mt-3">
                        <div class="card">
                            <div class="card-body" style="border: 1px solid">
                                <h3 class="card-title" style="font-size: 25px;"><b>Action Button</b></h3>
                                <div class="row">
                                    <div class="col-md-6">
                                        <form method="POST" action="">
                                            <button type="submit" name="reset" class="btn btn-warning">Reset Button</button>
                                        </form>
                                    </div>
                                    <div class="col-md-6">
                                        <button class="btn btn-info mb-3" onclick="location.reload()">Refresh Button</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body" style="border: 1px solid">
                        <div class="row">
                            <div class="col-md-12">
                            <?php 

                            $formattedAllowance = number_format($allowance, 2);
                            $formattedRemaining = number_format($remaining, 2);

                            echo "<h6>Monthly Allowance: {$formattedAllowance}</h6>"; 
                            echo "<h6>Remaining Balance: {$formattedRemaining}</h6>";

                        
                            ?>
                            </div>

                            <div class="col-md-12">
                                <form action="" method="POST">
                                    <table class="table table-bordered" style="max-width: 100%">
                                        <thead style="max-width: 100%">
                                            <tr>
                                                <th style='width: 40%'>Expenses</th>
                                                <th style='width: 40%'>Amount</th>
                                                <th style='width: 20%'>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                foreach ($_SESSION['transactions'] as $index => $transaction) {
                                                    $formattedAmount = number_format($transaction['amount'], 2);
                                                    echo "<tr>";
                                                    echo "<td>{$transaction['description']}</td>";
                                                    echo "<td>{$formattedAmount}</td>";
                                                    echo "<td><button type='submit' name='delete' value='{$index}' class='btn btn-danger'>Remove</button></td>";
                                                    echo "</tr>";
                                                    // $total += $transaction['amount'];
                                                }
                                                $formattedTotal = number_format($total, 2);
                                                echo "<tr><th>Total</th><th>{$formattedTotal}</th><th></th></tr>";

                                                $remaining = $allowance - $total;
                                                $formattedRemaining = number_format($remaining, 2);
                                            ?>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
