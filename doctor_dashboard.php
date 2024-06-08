<?php
// Initialize the session
session_start();

// Check if the doctor is not logged in, then redirect to the login page
if (!isset($_SESSION["doctor_id"])) {
    header("location: doctor_login.php");
    exit;
}

// Include database connection
include 'db.php';

// Define variables
$doctor_id = $_SESSION["doctor_id"];
$health_data = array();

// Fetch all health data for the doctor
$sql = "SELECT health_data.id, health_data.parameter_name, health_data.des, health_data.date_added, users.username
        FROM health_data
        INNER JOIN users ON health_data.user_id = users.id";

$result = $conn->query($sql);
if (!$result) {
    die("Error executing query: " . $conn->error);
}

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $health_data[] = $row;
    }
}

// Function to add tip for a health data entry
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['health_data_id']) && isset($_POST['tip'])) {
  
        $health_data_id = $_POST['health_data_id'];
        $tip = $_POST['tip'];
        $sql = "INSERT INTO tips (health_id, doctor_id, tip, date_added) VALUES ($health_data_id, $doctor_id, '$tip', NOW())";
        if ($conn->query($sql) === TRUE) {
            // Redirect to dashboard to avoid form resubmission
            header("Location: doctor_dashboard.php");
            exit;
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <div class="py-5 text-center">
            <h2>Doctor Dashboard</h2>
            
            <p>Welcome, <?php echo $_SESSION["username"]; ?>!</p>
        </div>
        <div class="row">
            <div class="col-md-12">

                <h3>User Health Data</h3>
                <p class="text-right"><a href="logout.php" class="btn btn-danger">Sign Out</a></p>

                <?php if (!empty($health_data)) : ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Issue In</th>
                                <th>Description</th>
                                <th>Date Added</th>
                                <th>Add Tip</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($health_data as $data) : ?>
    <tr>
        <td><?php echo $data['username']; ?></td>
        <td><?php echo $data['parameter_name']; ?></td>
        <td><?php echo $data['des']; ?></td>
        <td><?php echo $data['date_added']; ?></td>
        <td>
            <?php
            // Check if a tip exists for this health data entry
            $sql = "SELECT * FROM tips WHERE health_id = {$data['id']}";
            $tip_result = $conn->query($sql);
        
            if ($tip_result && $tip_result->num_rows > 0) {
                // Display existing tip
                $tip_row = $tip_result->fetch_assoc();

            
                echo "Tip: " . $tip_row['tip'];
            } else {
                // Display form to add tip
            ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <input type="hidden" name="health_data_id" value="<?php echo $data['id']; ?>">
                    <div class="form-group">
                        <input type="text" class="form-control" name="tip" placeholder="Enter Tip" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Tip</button>
                </form>
            <?php } ?>
        </td>
    </tr>
<?php endforeach; ?>

                        </tbody>
                    </table>
                <?php else : ?>
                    <p>No user health data found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>
