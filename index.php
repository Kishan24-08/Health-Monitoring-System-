<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        h1,
        h2 {
            color: #333333;
        }

        .table {
            background-color: #ffffff;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <h1>Health Dashboard</h1>
                <p><a href="logout.php" class="btn btn-danger">Sign Out</a></p>

                <?php 
                session_start();
                include 'db.php';

                // Check if user is logged in
                if (!isset($_SESSION['user_id'])) {
                    header("Location: login.php"); // Redirect to login page if not logged in
                    exit;
                }

                $user_id = $_SESSION['user_id'];

                // Fetch user's health parameters
                $sql = "SELECT * FROM health_data WHERE user_id = $user_id";
                $result = $conn->query($sql);

                $health_data_table = ''; // Initialize variable to store health data table HTML

                if ($result->num_rows > 0) {
                    $health_data_table .= "<div class='table-responsive'>";
                    $health_data_table .= "<table class='table table-striped'>";
                    $health_data_table .= "<thead><tr><th>Parameter Name</th><th>Description</th><th>Date Added</th><th>Tips</th></tr></thead>";
                    $health_data_table .= "<tbody>";
                    while ($row = $result->fetch_assoc()) {
                        $health_data_table .= "<tr><td>" . $row["parameter_name"] . "</td><td>" . $row["des"] . "</td><td>" . $row["date_added"] . "</td><td>";

                        // Fetch tips for this health data entry
                        $health_data_id = $row['id'];
                        $sql_tips = "SELECT * FROM tips WHERE health_id = $health_data_id";
                        $result_tips = $conn->query($sql_tips);
                        if (!$result_tips) {
                            die("Error executing query: " . $conn->error);
                        }
                        if ($result_tips->num_rows > 0) {
                            while ($tip_row = $result_tips->fetch_assoc()) {
                                $health_data_table .= "<div>Tip: " . $tip_row['tip'] . "</div>";
                            }
                        } else {
                            $health_data_table .= "<div>No tips available.</div>";
                        }

                        $health_data_table .= "</td></tr>";
                    }
                    $health_data_table .= "</tbody>";
                    $health_data_table .= "</table>";
                    $health_data_table .= "</div>";
                } else {
                    $health_data_table = "<p>No health data available.</p>";
                }

                $conn->close();

                echo $health_data_table;
                ?>
            </div>
            <div class="col-md-12">
                <h2>Add New Health Issue</h2>
                <form action="add_health_issue_process.php" method="post">
                    <div class="form-group">
                        <label for="parameter_name">Parameter Name</label>
                        <input type="hidden" id="parameter_name_hidden" name="parameter_name">
                        <select class="form-control" id="parameter_name_select">
                            <option>Select Option</option>
                            <option value="Blood Pressure">Blood Pressure</option>
                            <option value="Sugar Level">Sugar Level</option>
                            <option value="Heart Rate">Heart Rate</option>
                            <option value="Body Temperature">Body Temperature</option>
                            <option value="Cholesterol Level">Cholesterol Level</option>
                            <option value="Body Mass Index (BMI)">Body Mass Index (BMI)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="value">Description</label>
                        <textarea class="form-control" id="des" name="des" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#parameter_name_select').select2({
                placeholder: 'Select a parameter...',
                allowClear: true // Option to clear selection
            });

            // Event handler for changing the selected option
            $('#parameter_name_select').on('change', function() {
                var selectedValue = $(this).val();
                $('#parameter_name_hidden').val(selectedValue); // Set the hidden input value
            });
        });
    </script>
</body>

</html>
