<?php

include("config.php");
session_start();
$response = array();
$arrayOfDays = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat');

$jsonData = file_get_contents("php://input");
$dataArray = json_decode($jsonData, true);
$class = $dataArray['class'];
$section = $dataArray['section'];
$dayOfWeek = (int)$dataArray['dayOfWeak'];
$uid = $_SESSION['uid']; 

if ($dataArray !== null) {
    // Check if received data is valid
    if (isset($dataArray['data']) && !empty($dataArray['data'])) {
        $receivedData = $dataArray['data'];

        $response['status'] = 'success';
        $response['message'] = 'Timetable data received successfully';

        // Get the records from the database
        $query = 'SELECT `s_no` FROM `time_table` WHERE `class`=? AND `section`=?;';
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $class, $section);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);

        if (mysqli_num_rows($result) > 0) {
            $count = 0;
            $response['status'] = 'success';
            while ($row = mysqli_fetch_assoc($result)) {
                // Make sure day of week is within valid range (1-7)
                if ($dayOfWeek >= 1 && $dayOfWeek <= 7) {
                    // Prepare the update query
                    $sql = 'UPDATE `time_table` SET `start_time` = ?, `end_time` = ?, ' . $arrayOfDays[$dayOfWeek - 1] . ' = ?, `editor_id` = ?, `timestamp` = CURRENT_TIMESTAMP() WHERE `s_no` = ?;';
                    $stmt2 = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt2, "ssssi", $receivedData[$count]['startTime'], $receivedData[$count]['endTime'], $receivedData[$count]['subject'], $uid, $row['s_no']);
                    
                    // Execute the update query
                    if (mysqli_stmt_execute($stmt2)) {
                        $response['message'] = 'Timetable updated successfully.';
                    } else {
                        $response['status'] = 'error';
                        $response['message'] = 'Failed to update record at index ' . $count;
                        break;
                    }

                    mysqli_stmt_close($stmt2);
                    $count++;
                } else {
                    $response['status'] = 'error';
                    $response['message'] = 'Invalid day of the week';
                    break;
                }
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'No records found for the provided class and section';
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Received data is invalid or empty';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Failed to decode input data';
}

echo json_encode($response);

?>
