document.getElementById("findTimeTableBtn").addEventListener("click", function () {
    const classValue = document.getElementById("search-class").value;
    const sectionValue = document.getElementById("search-section").value;
    const dayValue = document.getElementById("search-day").value;  // Get selected day from the dropdown

    // Fetch data from the PHP script
    const url = `/school-management-system/assets/generateTimetable.php?class=${classValue}&section=${sectionValue}&day=${dayValue}`;

    console.log("Fetching URL:", url); // Debugging

    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error("Error fetching timetable");
            }
            return response.json();
        })
        .then(data => {
            console.log("Fetched data:", data); // Debugging
            displayTimetable(data, classValue, sectionValue, dayValue);
        })
        .catch(error => {
            console.error("Error fetching timetable:", error);
        });
});

function displayTimetable(timetable, classValue, sectionValue, dayValue) {
    const timetableTable = document.querySelector("#timeTable_table1");
    const timeTableClassSection = document.getElementById("timeTableClassSection");

    if (!timetableTable || !timeTableClassSection) {
        console.error("DOM elements for timetable not found");
        return;
    }

    // Clear existing rows
    timetableTable.innerHTML = "";

    // Set the title with class, section, and day
    timeTableClassSection.textContent = `Class: ${classValue}, Section: ${sectionValue}, Day: ${dayValue}`;

    // Populate timetable rows
    timetable.forEach(slot => {
        const row = document.createElement("tr");

        const startTimeCell = document.createElement("td");
        startTimeCell.textContent = slot.start_time;
        row.appendChild(startTimeCell);

        const endTimeCell = document.createElement("td");
        endTimeCell.textContent = slot.end_time;
        row.appendChild(endTimeCell);

        const subjectCell = document.createElement("td");
        subjectCell.textContent = slot.subject;
        row.appendChild(subjectCell);

        const teacherCell = document.createElement("td");
        teacherCell.textContent = slot.teacher || "N/A";
        row.appendChild(teacherCell);

        timetableTable.appendChild(row);
    });
}
