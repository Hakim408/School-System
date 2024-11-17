<?php 
// Database connection
include("config.php");

// Fetch class, section, and day from GET request
$class = $_GET['class'] ?? '';
$section = $_GET['section'] ?? '';
$day = $_GET['day'] ?? '';  // Add day to the query parameters

if (empty($class) || empty($section)) {
    die(json_encode(["error" => "Class and Section are required."]));
}

if (empty($day)) {
    die(json_encode(["error" => "Day is required."]));
}

// Fetch teacher details
$teacherQuery = "SELECT id, fname, lname, subject FROM teachers";
$teacherResult = $conn->query($teacherQuery);

if ($teacherResult->num_rows === 0) {
    die(json_encode(["error" => "No teachers found in the database."]));
}

$teachers = [];
while ($teacher = $teacherResult->fetch_assoc()) {
    $teachers[] = $teacher;
}

// Fetch timetable slots for the given class, section, and day
$timeSlotsQuery = "SELECT `start_time`, `end_time`, `mon`, `tue`, `wed`, `thu`, `fri`, `sat`
                   FROM `time_table`
                   WHERE `class` = '$class' AND `section` = '$section'";
$timeSlotsResult = $conn->query($timeSlotsQuery);

if ($timeSlotsResult->num_rows === 0) {
    die(json_encode(["error" => "No timetable found for the selected class and section."]));
}

$timetable = [];
$assignedTeachers = [];

// Assign subjects to teachers using a greedy algorithm
while ($slot = $timeSlotsResult->fetch_assoc()) {
    // Only filter the selected day (e.g., Monday)
    if (!empty($slot[$day])) {
        $subject = $slot[$day];

        // Find an available teacher for this subject
        foreach ($teachers as $teacher) {
            if ($teacher['subject'] === $subject && !in_array($teacher['id'], $assignedTeachers)) {
                // Assign this teacher to the subject and slot
                $assignedTeachers[] = $teacher['id'];
                $timetable[] = [
                    'start_time' => $slot['start_time'],
                    'end_time' => $slot['end_time'],
                    'subject' => $subject,
                    'teacher' => $teacher['fname'] . " " . $teacher['lname']
                ];
                break;
            }
        }
    }
}

// Set Content-Type header and output the JSON response
header('Content-Type: application/json');
echo json_encode($timetable);
?>
