<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="https://fstt.ac.ma/Portail2023/wp-content/uploads/2023/03/Untitled-3-300x300.png" sizes="192x192">
</head>
<body>
<ul class="logos">
    <li class="fst-logo">
        <img src="fst-1024x383.png" alt="FSTTLogo" style="width: 230px; height: 80px;">
    </li>
    <li class="uni-logo">
        <img src="logo-uae-1024x297.png" alt="UniLogo"style="width: 230px; height: 60px;">
    </li>
</ul>
    <form class="formulaire" action="" method="post">
        <div class="top-button">
        <input type="submit" name="logout" value="Logout" formaction="index.php">
        <input type="submit" name="addstudent" value="Add Student" formaction="addstudent.php">
        </div>
        <div class="container">
            <h1>Student List</h1>
            <div class="search-bar">
                <input type="text" name="search" placeholder="Search by name">
                <input type="submit" name="searchsubmit" value="Search">
            </div>
            <?php
                session_start();
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "students";
                $conn = new mysqli($servername, $username, $password, $dbname);
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }
                //bar dyal recherche 
                $sql = "SELECT * FROM student";
                if (isset($_POST['searchsubmit']) && !empty($_POST['search'])) {
                    $search = $conn->real_escape_string($_POST['search']);
                    $sql = "SELECT * FROM student WHERE fullname LIKE '%$search%'";
                }

                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                        echo "<div class='student-list'>";
                        echo "<table class='styled-table'>";
                        echo "<thead>";
                        echo "<tr>";
                        echo "<th>Fullname</th>";
                        echo "<th>Email</th>";
                        echo "<th>Filiere</th>";
                        echo "<th>Delete</th>";
                        echo "<th>Update</th>";
                        echo "</tr>";
                        echo "</thead>";
                        echo "<tbody>";

                    while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row["fullname"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["filiere"]) . "</td>";
                            echo "<td><button type='submit' name='delete' value='" . htmlspecialchars($row['id']) . "'>Delete</button></td>";
                            echo "<td><button type='submit' name='update' value='" . htmlspecialchars($row['id']) . "'>Update</button></td>";
                            echo "</tr>";
                        }
                        //delete button
                        if (isset($_POST['delete'])) {
                            $id = intval($_POST['delete']);
                            $sql = "DELETE FROM student WHERE id = $id";
                            if ($conn->query($sql) === TRUE) {
                                echo "Record deleted successfully";
                                header("Refresh:0");
                            } else {
                                echo "Error deleting record: {$conn->error}";
                            }
                        }
                        //update button
                        if (isset($_POST['update'])) {
                            $id = intval($_POST['update']);
                            $sql = "SELECT * FROM student WHERE id = $id";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                $row = $result->fetch_assoc();
                                $fullname = $row['fullname'];
                                $email = $row['email'];
                                $filiere = $row['filiere'];
                                header("Location: update.php?fullname=" . urlencode($fullname) . "&email=" . urlencode($email) . "&filiere=" . urlencode($filiere) . "&id=" . urlencode($id));
                            }
                            exit();
                        }
                        echo "</tbody>";
                        echo "</table>";
                        echo "</div>";
                } else {
                    echo "0 results";
                }
                $conn->close();
            ?>
        </div>
    </form>
    <footer style="text-align: center; position: absolute; bottom: 0; width: 100%; background-color: #ccc;"> © Made by EL GORRIM MOHAMED. LSI24/25</footer>
</body>
</html>