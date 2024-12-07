<?php
session_start();
ob_start(); // maerft dyalash hadi chatgpt gali nzidha
include 'db_connection.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if (!isset($_GET['email']) || empty($_GET['email'])) {
    die('Please enter from the link you have in your email.');
}
$emailFromUrl = filter_var($_GET['email'], FILTER_SANITIZE_EMAIL);
if (!filter_var($emailFromUrl, FILTER_VALIDATE_EMAIL)) {
    die('Invalid email address in URL.');
}

$errors = [];
$success = '';

// Process form submission
if(isset($_POST["submit"])) {
    // Sanitize inputs
    $fullname = htmlspecialchars(trim($_POST["fullname"]));
    $cin = htmlspecialchars(trim($_POST["cin"]));
    $cne = htmlspecialchars(trim($_POST["cne"]));
    $date = htmlspecialchars(trim($_POST["date"]));
    $genre = htmlspecialchars(trim($_POST["genre"]));
    $phone = htmlspecialchars(trim($_POST["phone"]));
    $address = htmlspecialchars(trim($_POST["address"]));
    $nationnalite = htmlspecialchars(trim($_POST["nationnalite"]));
    $annee = htmlspecialchars(trim($_POST["annee"]));
    $serie = htmlspecialchars(trim($_POST["seriebac"]));
    $mention = htmlspecialchars(trim($_POST["mention"]));
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $filiere = htmlspecialchars(trim($_POST["filiere"]));
    $finalChoice = htmlspecialchars(trim($_POST["final-choice"]));
    
    if ($email !== $emailFromUrl) {
        $errors[] = "Email mismatch detected.";
    }

    // Validation
    if(empty($fullname) || empty($email) || empty($cin) || empty($filiere) || 
       empty($cne) || empty($date) || empty($genre) || empty($phone) || 
       empty($address) || empty($nationnalite) || empty($annee) || 
       empty($serie) || empty($mention)) {
        $errors[] = "Please fill in all fields.";
    }

    $minDate = strtotime('2001-01-01');
    $inputDate = strtotime($date);
    $maxDate = strtotime(date("Y-m-d"));
    
    if($inputDate < $minDate || $inputDate > $maxDate) {
        $errors[] = "Invalid date of birth.";
    }

    if(!preg_match("/^[0-9]{10}$/", $phone)) {
        $errors[] = "Invalid phone number format.";
    }

    if(!preg_match("/^[A-Z]{1,2}[0-9]{6}$/i", $cin)) {
        $errors[] = "Invalid CIN format. Example format: AB123456";
    }

    if(!preg_match("/^[A-Z][0-9]{9}$/i", $cne)) {
        $errors[] = "Invalid CNE format. Should be 1 letter followed by 9 numbers";
    }

    $checkDuplicate = $dsn->prepare("SELECT email,cin,cne FROM student WHERE email = :email OR cin = :cin OR cne = :cne");
        $checkDuplicate->execute([
            ':email' => $email,
            ':cin' => $cin,
            ':cne' => $cne
        ]);

        
        $duplicate = $checkDuplicate->fetch(PDO::FETCH_ASSOC);
        
        if($duplicate) {
            if($duplicate['email'] === $email) {
                $errors[] = "This email is already registered.";
            }
            if($duplicate['cin'] === $cin) {
                $errors[] = "This CIN is already registered.";
            }
            if($duplicate['cne'] === $cne) {
                $errors[] = "This CNE is already registered.";
            }
        }
    // chof wach deja dar inscription 
    $stmt = $dsn->prepare("SELECT * FROM cycle WHERE email = :email");
    $stmt->bindParam(':email',$_POST["email"]);
    $stmt->execute();
    $stmt->fetchAll();

    $stmt2 = $dsn->prepare("SELECT * FROM master WHERE email = :email");
    $stmt2->bindParam(':email',$_POST["email"]);
    $stmt2->execute();
    $stmt2->fetchAll();

    if($stmt->rowCount() > 0 || $stmt2->rowCount() > 0) {
        $_SESSION['email'] = $email;
        header("Location: download.php");
    }else{
        // nzido student data f base desdonnées
    if(empty($errors)) {
        try {
            // Base des données d student en general
            $stmt = $dsn->prepare("INSERT INTO student (fullname, cin, cne, ddn, genre, telephone, 
                      ldn, nationalite, annee, serie, Mention, email, filiere, selected_program) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([$fullname, $cin, $cne, $date, $genre, $phone,
                          $address, $nationnalite, $annee, $serie, $mention, $email, $filiere, 
                          strtolower($finalChoice)]);
            
            $_SESSION['email'] = $email;
            if($finalChoice === "Master") {
                $_SESSION['success'] = "Application submitted successfully.";
                header("Location: mst.php");
                exit();
            } 
            else if($finalChoice === "Ci") {
                $_SESSION['success'] = "Application submitted successfully.";
                header("Location: ci.php");
                exit();
            }
        } catch (PDOException $e) {
            $errors[] = "Database Error: " . $e->getMessage();
            }
        }   
    }    
}
if(isset($_POST["delete"])) {
    try {
        $stmt = $dsn->prepare("DELETE FROM cycle WHERE email = ?");
        $stmt->execute([$emailFromUrl]);
        $stmt = $dsn->prepare("DELETE FROM master WHERE email = ?");
        $stmt->execute([$emailFromUrl]);
        $stmt = $dsn->prepare("DELETE FROM student WHERE email = ?");
        $stmt->execute([$emailFromUrl]);
        
        if($stmt->rowCount() > 0) {
            session_destroy();
            echo "<script>
                    alert('Application removed successfully');
                    window.location.href = 'index.php';
                  </script>";

                  $mail = prepareMailer();
                  $mail->addAddress($emailFromUrl, $fullnameFromUrl);
                  $mail->Subject = "Suppression de Candidature";
                  $mail->Body = "
                  <div style='font-family: Arial, sans-serif; text-align: center;'>
                      <img src='https://fstt.ac.ma/Portail2023/wp-content/uploads/2023/03/Untitled-3-300x300.png' alt='FSTT Logo' style='width: 100px; height: 100px;'>
                      <h2>Candidature Supprimée avec Succès</h2>
                      <p style='font-size: 1.2em;'>Cher(e) Candidat(e),</p>
                      <p style='font-size: 1.1em;'>Votre candidature à l'UAE a été supprimée avec succès de notre système.</p>
                      <p style='font-size: 1.1em;'>Si vous souhaitez postuler à nouveau dans le futur, vous devrez recommencer un nouveau processus de candidature.</p>
                      <p style='font-size: 0.9em; color: #666; margin-top: 20px;'>Cordialement,<br>Administration UAE</p>
                  </div>";
                  $mail->send();
            
            exit();
        } else {
            echo "<script>alert('No application found to remove');</script>";
        }
    } catch(PDOException $e) {
        error_log("Delete error: " . $e->getMessage());
        echo "<script>alert('Error removing application. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://fstt.ac.ma/Portail2023/wp-content/uploads/2023/03/Untitled-3-300x300.png" sizes="192x192">
    <link rel="stylesheet" href="style.css">
    <title>Add Student</title>
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
<form action="" method="post">
    <?php if (!empty($errors)): ?>
        <div class="error-messages" style="
            background-color: #ffe6e6; 
            color: #dc3545; 
            padding: 10px; 
            margin: 10px auto; 
            border: 1px solid #dc3545; 
            border-radius: 4px;
            width: max-content;
            min-width: 200px;
            max-width: 800px;
            text-align: center;">
            <?php foreach($errors as $error): ?>
                <p style="margin: 5px 0;"><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <div class="addstudent">
        <h1>Student Registration</h1>
        <div class="form-sections">
            <div class="section">
                <div class="section-title">Personal Information</div>
                <div class="input-group">
                    <label for="fullname">Full Name</label>
                    <input type="text" name="fullname" id="fullname" value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>">
                </div>
                <div class="input-group">
                    <label for="cin">CIN</label>
                    <input type="text" name="cin" id="cin" value="<?php echo isset($_POST['cin']) ? htmlspecialchars($_POST['cin']) : ''; ?>">
                </div>
                <div class="input-group">
                    <label for="cne">CNE</label>
                    <input type="text" name="cne" id="cne" value="<?php echo isset($_POST['cne']) ? htmlspecialchars($_POST['cne']) : ''; ?>">
                </div>
                <div class="input-group">
                    <label for="date">Date of Birth (mois/jour/annee)</label>
                    <input type="date" name="date" id="date" value="<?php echo isset($_POST['date']) ? htmlspecialchars($_POST['date']) : ''; ?>">
                </div>
                <div class="input-group">
                    <label for="genre">Gender</label>
                    <select name="genre" id="genre">
                        <option value="">Select Gender</option>
                        <option value="M" <?php echo (isset($_POST['genre']) && $_POST['genre'] === 'M') ? 'selected' : ''; ?>>Male</option>
                        <option value="F" <?php echo (isset($_POST['genre']) && $_POST['genre'] === 'F') ? 'selected' : ''; ?>>Female</option>
                    </select>
                </div>
            </div>
            <div class="section">
                <div class="section-title">Contact Information</div>
                <div class="input-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($emailFromUrl); ?>" readonly>
                </div>
                <div class="input-group">
                    <label for="phone">Phone</label>
                    <input type="tel" name="phone" id="phone" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                </div>
                <div class="input-group">
                    <label for="address">Address</label>
                    <input type="text" name="address" id="address" value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>">
                </div>
                <div class="input-group">
                    <label for="nationalite">Nationality</label>
                    <select size="1" name="nationnalite" id="nationnalite">
                        <?php
                        $nationalities = [
                            '350' => 'MAROCAINE',
                            '352' => 'ALGERIENNE',
                            '109' => 'ALLEMANDE',
                            '404' => 'AMERICAINE',
                            '415' => 'SPAIN',
                            '417' => 'FRANCE',
                            '428' => 'ITALIENNE',
                            '430' => 'TUNISIENNE',
                            '990' => 'AUTRE'
                        ];
                        foreach($nationalities as $value => $name) {
                            echo '<option value="' . $value . '"' . 
                                 (isset($_POST['nationnalite']) && $_POST['nationnalite'] == $value ? ' selected' : '') . 
                                 '>' . $name . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="section">
                <div class="section-title">Academic Information</div>
                <div class="input-group">
                    <label for="annee">Year of Baccalaureate</label>
                    <select name="annee" id="annee">                    
                <?php
                    $currentYear = date('Y');
                    echo "<option value='" . ($currentYear-2) . "'>" . ($currentYear-2) . "</option>";
                    echo "<option value='" . ($currentYear-3) . "'>" . ($currentYear-3) . "</option>";
                ?>
                </select>
                </div>
                <div class="input-group">
                    <label for="serie">Series</label>
                    <select size="1" name="seriebac" id="seriebac">
                        <option value="">Sélectionnez</option>
                        <?php
                        $series = [
                            'SMA' => 'Sciences Mathematiques A',
                            'SMB' => 'Sciences Mathematiques B',
                            'PC' => 'Sciences Physiques',
                            'SVT' => 'Sciences Vie et Terre',
                            'STE' => 'Sciences et technologies Electriques',
                            'STM' => 'Sciences et technologies Mécaniques',
                            'AUTRE' => 'Autre'
                        ];
                        foreach($series as $value => $name) {
                            echo '<option value="' . $value . '"' . 
                                 (isset($_POST['seriebac']) && $_POST['seriebac'] === $value ? ' selected' : '') . 
                                 '>' . $name . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="input-group">
                    <label for="mention">Mention</label>
                    <select name="mention" id="mention">
                        <option value="">Sélectionnez</option>
                        <option value="P">Passable</option>
				        <option value="AB">Assez Bien</option>
                        <option value="B">Bien</option>
                        <option value="TB">Trés Bien</option>
				        <option value="E">Exellent</option>
                        <option value="H">Honorable</option>
                        <option value="TH">Trés Honorable</option>
				        <option value="AU">Autre</option>
                    </select>
                </div>
            </div>

            <!-- Program Selection -->
            <div class="section">
                <div class="section-title">Program Selection</div>
                <div class="input-group">
                    <label for="filiere">Choose your Program</label>
                    <select name="filiere" id="filiere">
                        <option value="DEUST">DEUST</option>
                        <option value="DEUG">DEUG</option>
                        <option value="DEUT">DUT</option>
                        <option value="LST">LST</option>
                    </select>
                </div>
                <div class="final-choice">
                    <label for="final-choice">Choose your final choice</label>
                <select name="final-choice" id="final-choice">
                    <option value="Ci">Cycle D'Ingenieur</option>
                    <option value="Master" hidden>Master en Sciences Et Techniques</option>
                </select>
                </div>
            </div>
        </div>

        <div class="submit-section">
            <input type="submit" name="submit" value="Submit Application">
            <input type="submit" name="delete" value="Remove Application" 
           onclick="return confirm('Are you sure you want to remove your application? This cannot be undone.')" 
           style="background: linear-gradient(45deg, #dc3545, #c82333); margin-left: 10px;">
        </div>
    </div>
</form>
    </body>
</html>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('filiere').addEventListener('change', function() {
        var finalChoice = document.getElementById('final-choice');
        if (this.value === 'LST') {
            finalChoice.options[1].hidden = false;
        } else {
            finalChoice.options[1].hidden = true;
            finalChoice.value = 'Ci';
        }
    });
    document.getElementById('email').addEventListener('input', function(e) {
        e.preventDefault();
        this.value = '<?php echo htmlspecialchars($emailFromUrl); ?>';
        return false;
    });
});
</script>

<?php
ob_end_flush(); 
?>