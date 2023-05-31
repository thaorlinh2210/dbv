<html>
<head>
<link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>
    <?php
    session_start(); 

    if (!isset($_SESSION['employer'])) {
        $_SESSION['employer'] = '';
    }

    if (!isset($_SESSION['department'])) {
        $_SESSION['department'] = '';
    }

    if (!isset($_SESSION['staff'])) {
        $_SESSION['staff'] = '';
    }

    require_once("settings.php");
    $conn = mysqli_connect($host, $user, $pwd, $sql_db);

    if (!$conn) {
        echo "<p>Error: Database connection failure</p>";
        exit;
    }

    ?>

    <h3>Employer and Department</h3>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <select name="employer">
            <option value="">Select Employer</option>
            <?php
            $query = "SELECT * FROM Employer";
            $result = mysqli_query($conn, $query);

            if (!$result) {
                echo "<option disabled>Error retrieving employers</option>";
            } else {
                while ($row = mysqli_fetch_assoc($result)) {
                    $employerID = $row['Employer_ID'];
                    $employerName = $row['Employer_Name'];

                    $selected = ($_SESSION['employer'] == $employerID) ? 'selected' : '';

                    echo "<option value=\"$employerID\" $selected>$employerName</option>";
                }
            }
            ?>
        </select>

        <select name="department">
            <option value="">Select Department</option>
            <?php
            $query = "SELECT * FROM Department";
            $result = mysqli_query($conn, $query);

            if (!$result) {
                echo "<option disabled>Error retrieving departments</option>";
            } else {
                while ($row = mysqli_fetch_assoc($result)) {
                    $departmentId = $row['Department_ID'];
                    $departmentDesc = $row['Department_Description'];

                    $selected = ($_SESSION['department'] == $departmentId) ? 'selected' : '';

                    echo "<option value=\"$departmentId\" $selected>$departmentDesc</option>";
                }
            }
            ?>
        </select>

        <input type="submit" name="submitEmployerDepartment" value="Submit">
    </form>

    <?php
    if (isset($_POST['submitEmployerDepartment'])) {
        $_SESSION['employer'] = $_POST['employer'];
        $_SESSION['department'] = $_POST['department'];
    }

    if ($_SESSION['employer'] && $_SESSION['department']) {
        echo "<h3 id=\"staff\">Staff</h3>";
        echo "<form method=\"post\" action=\"" . htmlspecialchars($_SERVER["PHP_SELF"]) . "#staff\">";
        echo "<select name=\"staff\">";
        echo "<option value=\"\">Select Staff</option>";

        $selectedEmployer = $_SESSION['employer'];
        $selectedDepartment = $_SESSION['department'];

        $query1 = "SELECT Staff_ID FROM Employer_To_Staff WHERE Employer_ID = $selectedEmployer";
        $result1 = mysqli_query($conn, $query1);

        if (!$result1) {
            echo "<option disabled>Error retrieving staff</option>";
        } else {
            while ($row1 = mysqli_fetch_assoc($result1)) {
                $staffID = $row1['Staff_ID'];

                $query2 = "SELECT First_Name, Last_Name FROM Staff_Information WHERE Staff_ID = $staffID AND Department_ID = $selectedDepartment";
                $result2 = mysqli_query($conn, $query2);

                if (!$result2) {
                    echo "<option disabled>Error retrieving data for staff ID: $staffID</option>";
                } else {
                    while ($row2 = mysqli_fetch_assoc($result2)) {
                        $Firstname = $row2['First_Name'];
                        $Lastname = $row2['Last_Name'];

                        $selectedStaff = ($_SESSION['staff'] == $staffID) ? 'selected' : '';

                        echo "<option value=\"$staffID\" $selectedStaff>$Firstname $Lastname</option>";
                    }
                }
            }
        }

        echo "</select>";
        echo "<input type=\"submit\" name=\"submitStaff\" value=\"Submit\">";
        echo "</form>";
    }

    if (isset($_POST['submitStaff'])) {
        $_SESSION['staff'] = $_POST['staff'];
    }

    if ($_SESSION['employer'] && $_SESSION['department']) {
        echo "<h3>Add Shift</h3>";
        
        $selectedStaffID = $_SESSION['staff'];
        $totalHours = 0;

        echo "<form method=\"post\" action=\"" . htmlspecialchars($_SERVER["PHP_SELF"]) . "\">";
        echo "<select name=\"addshift\">";
        echo "<option value=\"\">Select Shift</option>";

        $getopenshift = "SELECT Shift_ID, Shift_Date, Start_time, End_time FROM Schedule WHERE Staff_ID IS NULL";
        $getopenshiftresult = mysqli_query($conn, $getopenshift);

        if (!$getopenshiftresult) {
            echo "<option disabled>Error retrieving open shifts</option>";
        } else {
            while ($row = mysqli_fetch_assoc($getopenshiftresult)) {
                $Shift_ID = $row['Shift_ID'];
                $Shift_Date = $row['Shift_Date'];
                $Start_Time = $row['Start_time'];
                $End_Time = $row['End_time'];

                echo "<option value=\"$Shift_ID\">$Shift_Date - $Start_Time to $End_Time</option>";
            }
        }

        echo "</select>";
        echo "<input type=\"hidden\" name=\"staffID\" value=\"$selectedStaffID\">";
        echo "<input type=\"submit\" name=\"submitShift\" value=\"Add Shift\">";
        echo "</form>";

        echo "<h4>Current Shifts:</h4>";

        $getcurrentshifts = "SELECT s.Shift_ID, s.Shift_Date, s.Start_time, s.End_time, si.First_Name, si.Last_Name 
                             FROM Schedule s 
                             JOIN Staff_Information si ON s.Staff_ID = si.Staff_ID 
                             WHERE s.Staff_ID = $selectedStaffID";
        $getcurrentshiftsresult = mysqli_query($conn, $getcurrentshifts);

        if (!$getcurrentshiftsresult) {
            echo "<p>Error retrieving current shifts: " . mysqli_error($conn) . "</p>";
        } else {
            if (mysqli_num_rows($getcurrentshiftsresult) > 0) {
                while ($row = mysqli_fetch_assoc($getcurrentshiftsresult)) {
                    $Shift_ID = $row['Shift_ID'];
                    $Shift_Date = $row['Shift_Date'];
                    $Start_Time = $row['Start_time'];
                    $End_Time = $row['End_time'];
                    $Staff_Firstname = $row['First_Name'];
                    $Staff_Lastname = $row['Last_Name'];

                    echo "<p>$Shift_Date - $Start_Time to $End_Time (Assigned to: $Staff_Firstname $Staff_Lastname)
                          <form method=\"post\" action=\"" . htmlspecialchars($_SERVER["PHP_SELF"]) . "\">
                            <input type=\"hidden\" name=\"removeShift\" value=\"$Shift_ID\">
                            <input type=\"submit\" value=\"Remove\">
                          </form>
                          </p>";

                    // Calculate shift duration in hours
                    $startTime = strtotime($Start_Time);
                    $endTime = strtotime($End_Time);
                    $duration = ($endTime - $startTime) / (60 * 60); // Convert seconds to hours
                    $totalHours += $duration;
                }
            } else {
                echo "<p>No current shifts found.</p>";
            }
        }

        $totalHours = abs($totalHours);
        $totalHours = floor($totalHours);

        echo "<p>Total Hours: $totalHours</p>";

        if ($totalHours >= 40) {
            echo "<p>Error: Maximum hours reached.</p>";
        }
    }

    if (isset($_POST['removeShift'])) {
        $removeShiftID = $_POST['removeShift'];
        $removeshift = "UPDATE Schedule SET Staff_ID = NULL WHERE Shift_ID = $removeShiftID";
        $removeshiftresult = mysqli_query($conn, $removeshift);

        if ($removeshiftresult) {
            echo "<p>Shift removed successfully.</p>";
        } else {
            echo "<p>Error removing shift: " . mysqli_error($conn) . "</p>";
        }
    }

    if (isset($_POST['submitShift'])) {
        $addShiftID = $_POST['addshift'];
        $staffID = $_POST['staffID'];

        $addShift = "UPDATE Schedule SET Staff_ID = $staffID WHERE Shift_ID = $addShiftID";
        $addShiftResult = mysqli_query($conn, $addShift);

        if ($addShiftResult) {
            echo "<p>Shift added successfully.</p>";
        } else {
            echo "<p>Error adding shift: " . mysqli_error($conn) . "</p>";
        }
    }

    mysqli_close($conn);
    ?>
</body>
</html>
