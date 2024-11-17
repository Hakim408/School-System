<?php
include("config.php");
session_start();
$response = array();
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $uid = $_SESSION['uid'];

    // Get the raw POST data from the request body
    $jsonData = file_get_contents('php://input');
    $decodedData = json_decode($jsonData, true);

    if (isset($decodedData["dayOfWeak"])) {

        $dayOfWeak = (int)($decodedData['dayOfWeak']);
        $class = $decodedData['class'];
        $section = $decodedData['section'];

        $arrayOfDays = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');

        // Query to get the timetable for the specified class and section
        $query = "SELECT * FROM `time_table` WHERE `class`=? AND `section`=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $class, $section);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);

        if (mysqli_num_rows($result) > 0) {
            $response['status'] = "success";
            $response['table1Message'] = "";
            $response['table2Message'] = "";
            $response['day'] = "working_day"; // Default is working day

            $editorId = "";
            $editingTime = "";

            $flag = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                $timestamp = $row['timestamp'];
                $editingTime = date('d M, Y', strtotime($timestamp));
                $editorId = $row['editor_id'];

                if ($dayOfWeak < 7) {
                    // Distribute timetable into two tables for better visual representation
                    if ($flag < 5) {
                        // First half of the day (e.g., morning sessions)
                        $response['table1Message'] .= '  
                        <tr class="tableRow">
                            <td class="tableData"> <input class="form-control tableInput srartTime_" type="text" value="' . $row['start_time'] . '" disabled></td>
                            <td class="tableData"><input class="form-control tableInput endTime_" type="text" value="' . $row['end_time'] . '" disabled></td>
                            <td class="tableData"> <input class="form-control tableInput subject_" type="text" value="' . $row[$arrayOfDays[$dayOfWeak - 1]] . '" disabled></td>
                        </tr>';
                    } else {
                        // Second half of the day (e.g., afternoon sessions)
                        $response['table2Message'] .= '  
                        <tr class="tableRow">
                            <td class="tableData"> <input class="form-control tableInput srartTime_" type="text" value="' . $row['start_time'] . '" disabled></td>
                            <td class="tableData"><input class="form-control tableInput endTime_" type="text" value="' . $row['end_time'] . '" disabled></td>
                            <td class="tableData"> <input class="form-control tableInput subject_" type="text" value="' . $row[$arrayOfDays[$dayOfWeak - 1]] . '" disabled></td>
                        </tr>';
                    }
                } else {
                    $response['day'] = "sunday";  // Set day to Sunday if no classes
                }

                $flag++;
            }

            // Fetch the editor's name
            $query = "SELECT CONCAT(`fname`, ' ', `lname`) AS full_name FROM `admins` WHERE `admins`.`id`=? UNION SELECT CONCAT(`fname`, ' ', `lname`) AS full_name FROM `teachers`  WHERE `teachers`.`id`=? ; ";
            $stmt2 = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt2, "ss", $editorId, $editorId);
            mysqli_stmt_execute($stmt2);
            $result2 = mysqli_stmt_get_result($stmt2);
            mysqli_stmt_close($stmt2);

            $editorFullName = "";
            if (mysqli_num_rows($result2) > 0) {
                $row2 = mysqli_fetch_assoc($result2);
                $editorFullName = ucfirst(strtolower($row2['full_name']));   
            } else {
                $editorFullName = "REMOVED";  // Editor not found
            }

            if($editorId == $uid){
                $editorFullName = "You";  // Display "You" for the current user
            }

            $response['editorName'] = $editorFullName;
            $response['editingTime'] = $editingTime;

        } else {
            // If no timetable data exists for this class/section, create a new one
            for ($i = 0; $i < 8; $i++) {
                $query3 = "INSERT INTO `time_table` (`s_no`, `class`, `section`, `start_time`, `end_time`, `mon`, `tue`, `wed`, `thu`, `fri`, `sat`, `editor_id`, `timestamp`) 
                VALUES (NULL, ?, ?, '', '', '', '', '', '', '', '', ?, current_timestamp());";

                $stmt3 = mysqli_prepare($conn, $query3);
                mysqli_stmt_bind_param($stmt3, "sss", $class, $section, $uid);
                mysqli_stmt_execute($stmt3);
                mysqli_stmt_close($stmt3);
            }

            $response['status'] = "creating";  // Status for creating a new timetable
            $response['class'] = $class;
            $response['section'] = $section;
        }

    } else {
        $response['status'] = "Error";
        $response['message'] = "Something went wrong!";
    }
} else {
    $response['status'] = "Error";
    $response['message'] = "Something went wrong!";
}

echo json_encode($response);
?>
