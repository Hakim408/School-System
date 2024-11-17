<?php include("../assets/noSessionRedirect.php"); ?>
<?php include("./verifyRoleRedirect.php"); ?>
<?php include('partials/_header.php') ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="style.css">
    <title>School Management - Timetable</title>
</head>

<body>
    <!-- Sidebar -->
    <?php include('partials/_sidebar.php') ?>
    <!-- End Sidebar -->

    <!-- Main Content -->
    <div class="content">
        <!-- Navbar -->
        <?php include("partials/_navbar.php"); ?>
        <!-- End Navbar -->

        <main>
            <div class="header">
                <h1>Time Table</h1>
            </div>

            <!-- Body -->
            <div class="bottom-data">
                <div class="timetable-form">
                    <div class="form-group">
                        <label for="search-class">Class</label>
                        <select class="form-select" id="search-class">
                            <?php include('partials/select_classes.php'); ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="search-section">Section</label>
                        <select class="form-select" id="search-section">
                            <option value="A" selected>A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                        </select>
                    </div>

                    <!-- Add a Day Dropdown -->
<div class="form-group">
    <label for="search-day">Day</label>
    <select class="form-select" id="search-day">
        <option value="mon">Monday</option>
        <option value="tue">Tuesday</option>
        <option value="wed">Wednesday</option>
        <option value="thu">Thursday</option>
        <option value="fri">Friday</option>
        <option value="sat">Saturday</option>
    </select>
</div>


                    <button class="btn btn-primary" id="findTimeTableBtn">Generate</button>
                </div>

                <div class="timetable-container">
                    <h3 id="timeTableClassSection"></h3>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Period Start</th>
                                <th>Period End</th>
                                <th>Subject</th>
                                <th>Teacher</th>
                            </tr>
                        </thead>
                        <tbody id="timeTable_table1"></tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script src="../assets/js/timetable.js"></script>
</body>

</html>
