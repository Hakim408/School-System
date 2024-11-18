<?php
$searches = array('dashboard', 'teacher', 'student', 'subjects', "attendence", 'noticeboard', 'timetable', 'syllabus', 'notes', 'marks','bus service', 'settings');
$pages = array('dashboard.php', 'teacher.php', 'student.php', 'subjects.php', "attendence.php", 'noticeboard.php', 'timetable.php', 'syllabus.php', 'notes.php', 'marks.php','buses.php', 'settings.php');

// Create an associative array to map searches to pages
$searchMap = array_combine($searches, $pages);

// Sort the array alphabetically by the search keys
ksort($searchMap);

$searches = array_keys($searchMap); // Sorted keys
$pages = array_values($searchMap); // Sorted values

$response = "";

if (isset($_POST['searchValue'])) {
    $searchValue = $_POST['searchValue'];

    // Binary search implementation
    $low = 0;
    $high = count($searches) - 1;

    while ($low <= $high) {
        $mid = floor(($low + $high) / 2);
        if (strpos($searches[$mid], $searchValue) !== false) {
            echo $pages[$mid];
            exit();
        } elseif ($searchValue < $searches[$mid]) {
            $high = $mid - 1;
        } else {
            $low = $mid + 1;
        }
    }

    $response = "NOTFOUND";
}

echo $response;
?>
