<?php
// Include necessary files
include("../assets/noSessionRedirect.php");
include("./verifyRoleRedirect.php");
include('partials/config.php');  // Database connection

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $class = $_POST['class'];
    $section = $_POST['section'];
    $teacher_id = $_POST['teacher_id'];
    $day = $_POST['day'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $subject = $_POST['subject'];

    // Debugging: Output the POST data
    echo "<pre>";
    var_dump($_POST);
    echo "</pre>";

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO new_time_table (class, section, teacher_id, day, start_time, end_time, subject) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    // Check if preparation failed
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    // Bind parameters to the query
    if (!$stmt->bind_param("sssssss", $class, $section, $teacher_id, $day, $start_time, $end_time, $subject)) {
        die("Error binding parameters: " . $stmt->error);
    }

    // Execute the statement
    if ($stmt->execute()) {
        $successMessage = "Timetable entry added successfully!";
    } else {
        $errorMessage = "Error executing the query: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();

    // Ensure connection is closed after all queries
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../images/1.png">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="style.css">
    <title>School Management</title>
</head>

<body>

    <div class='toast-container position-fixed text-success bottom-0 end-0 p-3'>
        <div id='liveToast' class='toast' role='alert' aria-live='assertive' aria-atomic='true' style="color:black;">
            <div class='d-flex'>
                <div class='toast-body' id="toast-alert-message">
                    <?php
                    if (isset($successMessage)) {
                        echo $successMessage;
                    } elseif (isset($errorMessage)) {
                        echo $errorMessage;
                    }
                    ?>
                </div>
                <button type='button' class='btn-close me-2 m-auto text-danger' data-bs-dismiss='toast' aria-label='Close'></button>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <?php include('partials/_sidebar.php') ?>
    <input type="hidden" value="7" id="checkFileName">
    <!-- End of Sidebar -->

    <!-- Main Content -->
    <div class="content">
        <!-- Navbar -->
        <?php include("partials/_navbar.php"); ?>
        <!-- End of Navbar -->

        <div class="main-content">
            <div class="container mt-5">
                <h2 class="text-center">Add Timetable Entry</h2>

                <form method="POST" action="newtimetable.php">
                    <div class="row">
                        <div class="col-md-6">
                            <!-- Class Selection -->
                            <div class="form-group mb-3">
                                <label for="class">Class:</label>
                                <select class="form-control" id="class" name="class" required>
                                    <?php
                                    include("partials/select_classes.php");
                                    // Fetch classes from the database
                                    $result = $conn->query("SELECT * FROM classes");
                                    while ($class = $result->fetch_assoc()) {
                                        echo "<option value='" . $class['class_name'] . "'>" . $class['class_name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <!-- Section Selection -->
                            <div class="form-group mb-3">
                                <label for="section">Section:</label>
                                <select class="form-control" id="section" name="section" required>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <!-- Teacher Selection -->
                            <div class="form-group mb-3">
                                <label for="teacher_id">Teacher:</label>
                                <select class="form-control" id="teacher_id" name="teacher_id" required>
                                    <?php
                                    // Fetch teachers from the database
                                    $result = $conn->query("SELECT * FROM teachers");
                                    while ($teacher = $result->fetch_assoc()) {
                                        echo "<option value='" . $teacher['id'] . "'>" . $teacher['fname'] . " " . $teacher['lname'] . " - " . $teacher['subject'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <!-- Day Selection -->
                            <div class="form-group mb-3">
                                <label for="day">Day:</label>
                                <select class="form-control" id="day" name="day" required>
                                    <option value="Monday">Monday</option>
                                    <option value="Tuesday">Tuesday</option>
                                    <option value="Wednesday">Wednesday</option>
                                    <option value="Thursday">Thursday</option>
                                    <option value="Friday">Friday</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <!-- Start Time Selection -->
                            <div class="form-group mb-3">
                                <label for="start_time">Start Time:</label>
                                <input type="time" id="start_time" name="start_time" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <!-- End Time Selection -->
                            <div class="form-group mb-3">
                                <label for="end_time">End Time:</label>
                                <input type="time" id="end_time" name="end_time" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <!-- Subject Input -->
                    <div class="form-group mb-3">
                        <label for="subject">Subject:</label>
                        <input type="text" id="subject" name="subject" class="form-control" placeholder="Enter subject (e.g., Hindi)" required>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary btn-block mt-4">Add Timetable Entry</button>
                </form>
            </div>
        </div>
    </div>

    <script src="../assets/js/timetable.js"></script>
    <?php include('partials/_footer.php'); ?>
</body>

</html>
