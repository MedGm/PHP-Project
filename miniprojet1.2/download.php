<?php 
session_start();
require_once 'db_connection.php';

// Initialize variables
$error = null;
$userDataci = null;
$userDatamst = null;

try {
    // Check if email exists in session
    if(!isset($_SESSION['email']) || empty($_SESSION['email'])) {
        // Try to get email from POST if it was just submitted
        if(isset($_POST['email']) && !empty($_POST['email'])) {
            $_SESSION['email'] = $_POST['email'];
        } else {
            throw new Exception("Session expired. Please log in again.");
        }
    }

    $email = $_SESSION['email'];
    
    // Check if email exists in either table
    $sql1 = "SELECT * FROM cycle WHERE email = :email";
    $stmtci = $dsn->prepare($sql1);
    $stmtci->bindParam(':email', $email);
    $stmtci->execute();
    $userDataci = $stmtci->fetch(PDO::FETCH_ASSOC);
    
    $sql2 = "SELECT * FROM master WHERE email = :email";
    $stmtm = $dsn->prepare($sql2);
    $stmtm->bindParam(':email', $email);
    $stmtm->execute();
    $userDatamst = $stmtm->fetch(PDO::FETCH_ASSOC);

    if(!$userDataci && !$userDatamst) {
        // Also check the student table
        $sql3 = "SELECT * FROM student WHERE email = :email";
        $stmt3 = $dsn->prepare($sql3);
        $stmt3->bindParam(':email', $email);
        $stmt3->execute();
        $studentData = $stmt3->fetch(PDO::FETCH_ASSOC);

        if(!$studentData) {
            throw new Exception("No registration data found for this email.");
        }
    }

    function displayImageInPDF($pdf, $imageData) {
        if (!empty($imageData)) {
            try {
                // katjdb les info d image mn binary data li sjlnaha f databasewha
                $imageInfo = getimagesizefromstring($imageData);
                if ($imageInfo === false) {
                    throw new Exception("Invalid image data");
                }
    
                // t7aded  type d l'image
                $imageType = '';
                switch ($imageInfo[2]) {
                    case IMAGETYPE_JPEG:
                        $extension = '.jpg';
                        $imageType = 'JPEG';
                        break;
                    case IMAGETYPE_PNG:
                        $extension = '.png';
                        $imageType = 'PNG';
                        break;
                    default:
                        throw new Exception("Unsupported image type");
                }
    
                // creer wahd temporary file
                $tempFile = tempnam(sys_get_temp_dir(), 'img') . $extension;
                file_put_contents($tempFile, $imageData);
                
        // dimension dyal image
        $maxWidth = 30;
        $maxHeight = 40;
        $ratio = min($maxWidth / $imageInfo[0], $maxHeight / $imageInfo[1]);
        $width = $imageInfo[0] * $ratio;
        $height = $imageInfo[1] * $ratio;

        // Ajouter image n PDF - updated Y position from 60 to 80
        $pdf->Image($tempFile, 160, 80, $width, $height, $imageType);
        
        // Cleanup
        unlink($tempFile);
        
        return true;
    } catch (Exception $e) {
        return false;
    }
}
return false;
}

    if($userDataci) {
        if(isset($_POST['submit'])){
            require 'vendor/setasign/fpdf/fpdf.php';

            $pdf = new FPDF();
            $pdf->AddPage();

            // Logos
            $pdf->Image('fst-1024x383.png', 10, 10, 50);
            $pdf->Image('logo-uae-1024x297.png', 150, 10, 50);

            
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Ln(30);
            $pdf->Cell(0, 10, "UNIVERSITE ABDELMALEK SAADI", 0, 1, 'C');
            $pdf->SetFont('Arial', 'B', 13);
            $pdf->Cell(0, 10, "Faculte des Sciences et Techniques de Tanger", 0, 1, 'C');
            $pdf->Cell(0, 10, "Fiche de candidature au Cycle d'Ingenieur", 0, 1, 'C');
            $pdf->SetFont('Arial', 'B', 13);
            $pdf->Cell(0, 10, "Numero de dossier : " . $userDataci['id'], 0, 1, 'C');
            $pdf->Ln(10);// Add extra space before image

            if (displayImageInPDF($pdf, $userDataci['user_image'])) {
                $pdf->Ln(5);
            } else {
                $pdf->Ln(20);
            }

            // Personal Information 
            $pdf->SetFont('Arial', 'B', 13);
            $pdf->Cell(0, 10, 'Informations Personnelles:', 0, 1);
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(60, 8, 'Nom et Prenom:', 0);
            $pdf->Cell(0, 8, $userDataci['fullname'], 0, 1);
            $pdf->Cell(60, 8, 'CIN:', 0);
            $pdf->Cell(0, 8, $userDataci['cin'], 0, 1);
            $pdf->Cell(60, 8, 'CNE:', 0);
            $pdf->Cell(0, 8, $userDataci['cne'], 0, 1);

            // Contact Information 
            $pdf->Ln(5);
            $pdf->SetFont('Arial', 'B', 13);
            $pdf->Cell(0, 10, 'Coordonnees:', 0, 1);
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(60, 8, 'Email:', 0);
            $pdf->Cell(0, 8, $userDataci['email'], 0, 1);
            $pdf->Cell(60, 8, 'Telephone:', 0);
            $pdf->Cell(0, 8, '+212 '.$userDataci['telephone'], 0, 1);

            // Academic Information 
            $pdf->Ln(5);
            $pdf->SetFont('Arial', 'B', 13);
            $pdf->Cell(0, 10, 'Informations Academiques:', 0, 1);
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(60, 8, 'Filiere:', 0);
            $pdf->Cell(0, 8, $userDataci['filiere'], 0, 1);
            $pdf->Cell(60, 8, 'Moyenne:', 0);
            $pdf->Cell(0, 8, $userDataci['moyen1'], 0, 1);
            $pdf->Cell(60, 8, 'Choix:', 0);
            $pdf->Cell(0, 8, $userDataci['classement'], 0, 1);

            // lqa3
            $currentY = $pdf->GetY();
            if ($currentY > 250) {
                $pdf->AddPage();
            }
            $pdf->SetY(max($currentY, 265)); 
            $pdf->SetFont('Arial', 'I', 10);
            $pdf->Cell(0, 10, 'www.fstt.ac.ma', 0, 1, 'C');
            $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());

            $pdf->Output($userDataci['fullname'].'_'.$userDataci['cne'].'.pdf', 'D');
            header('Location: download.php');
            exit();
        }
    } elseif($userDatamst) {
        if(isset($_POST['submit'])){
            require 'vendor/setasign/fpdf/fpdf.php'; //hadi l library dyal pdf
        
            $pdf = new FPDF();
            $pdf->AddPage(); //hadchi dyal creation d'une page
        
            // logos
            $pdf->Image('fst-1024x383.png', 10, 10, 50);
            $pdf->Image('logo-uae-1024x297.png', 150, 10, 50);

            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Ln(30);
            $pdf->Cell(0, 10, "UNIVERSITE ABDELMALEK SAADI", 0, 1, 'C');
            $pdf->SetFont('Arial', 'B', 13);
            $pdf->Cell(0, 10, "Faculte des Sciences et Techniques de Tanger", 0, 1, 'C');
            $pdf->Cell(0, 10, "Fiche de candidature au Master Sciences et Techniques", 0, 1, 'C');
            $pdf->SetFont('Arial', 'B', 13);
            $pdf->Cell(0, 10, "Numero de dossier : " . $userDatamst['id'], 0, 1, 'C');
            $pdf->Ln(10);

            if (displayImageInPDF($pdf, $userDatamst['user_image'])) {
                $pdf->Ln(10);
            } else {
                $pdf->Ln(20);
            }

            // Personal Information
            $pdf->SetFont('Arial', 'B', 13);
            $pdf->Cell(0, 10, 'Informations Personnelles:', 0, 1);
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(60, 8, 'Nom et Prenom:', 0);
            $pdf->Cell(0, 8, $userDatamst['fullname'], 0, 1);
            $pdf->Cell(60, 8, 'CIN:', 0);
            $pdf->Cell(0, 8, $userDatamst['cin'], 0, 1);
            $pdf->Cell(60, 8, 'CNE:', 0);
            $pdf->Cell(0, 8, $userDatamst['cne'], 0, 1);

            // Contact Information
            $pdf->Ln(5);
            $pdf->SetFont('Arial', 'B', 13);
            $pdf->Cell(0, 10, 'Coordonnees:', 0, 1);
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(60, 8, 'Email:', 0);
            $pdf->Cell(0, 8, $userDatamst['email'], 0, 1);
            $pdf->Cell(60, 8, 'Telephone:', 0);
            $pdf->Cell(0, 8, '+212 '.$userDatamst['telephone'], 0, 1);

            // Academic Information 
            $pdf->Ln(5);
            $pdf->SetFont('Arial', 'B', 13);
            $pdf->Cell(0, 10, 'Informations Academiques:', 0, 1);
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(60, 8, 'Filiere:', 0);
            $pdf->Cell(0, 8, $userDatamst['filiere'], 0, 1);
            $pdf->Cell(60, 8, 'Moyenne de DEUST/DEUG:', 0);
            $pdf->Cell(0, 8, $userDatamst['moyen1'], 0, 1);
            $pdf->Cell(60, 8, 'Moyenne de LST:', 0);
            $pdf->Cell(0, 8, $userDatamst['moyen2'], 0, 1);
            $pdf->Cell(60, 8, 'Choix:', 0);
            $pdf->Cell(0, 8, $userDatamst['classement'], 0, 1);

            // lfooter
            $currentY = $pdf->GetY();
            if ($currentY > 265) {
                $pdf->AddPage();
            }
            $pdf->SetY(max($currentY, 265)); 
            $pdf->SetFont('Arial', 'I', 10);
            $pdf->Cell(0, 10, 'www.fstt.ac.ma', 0, 1, 'C');
            $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());

            $pdf->Output($userDatamst['fullname'].'_'.$userDatamst['cne'].'.pdf', 'D'); //hna output dyal pdf fin kadir smytou w wach kaybda telecharger wla la
            header('Location :download.php');
            exit();
        }
    }
    
} catch(Exception $e) {
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://fstt.ac.ma/Portail2023/wp-content/uploads/2023/03/Untitled-3-300x300.png" sizes="192x192">
    <title>Download</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px;
        }
        input[type="submit"] {
            padding: 12px 24px;
            border-radius: 5px;
            cursor: pointer;
            border: none;
            font-size: 16px;
            background-color: #007bff;
            color: white;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .download-form {
            text-align: center;
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <form action="" method="post">
        <ul class="logos">
            <li class="fst-logo">
                <img src="fst-1024x383.png" alt="FSTTLogo" style="width: 230px; height: 80px;">
            </li>
            <li class="uni-logo">
                <img src="logo-uae-1024x297.png" alt="UniLogo" style="width: 230px; height: 60px;">
            </li>
        </ul>

        <div class="container">
            <?php if(isset($error)): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
                <p><a href="index.php">Return to login page</a></p>
            <?php elseif($userDataci || $userDatamst): ?>
                <h1>Formulaire d'inscription</h1>
                <div class="download-form">
                    <input type="submit" name="submit" value="Download">
                </div>
            <?php else: ?>
                <p>Please complete your registration first.</p>
                <?php if(isset($studentData)): ?>
                    <p><a href="<?php echo $studentData['filiere'] === 'LST' ? 'mst.php' : 'ci.php'; ?>">Continue Registration</a></p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </form>
</body>
</html>