<?php 
    session_start();
    require_once 'db_connection.php';

    $error = null;
try{
if(isset($_SESSION['email'])){
    $email = $_SESSION['email'];
    
        
        $sql1 = "SELECT * FROM cycle WHERE email = :email";
        $sql2 ="SELECT * FROM master WHERE email = :email";
        //kiqaleb f base des donnees d cycle
        $stmtci = $dsn->prepare($sql1);
        $stmtci->bindParam(':email', $email);
        $stmtci->execute();
        $userDataci = $stmtci->fetch(PDO::FETCH_ASSOC);
        //db kiqaleb f base des donnees de master
        $stmtm = $dsn->prepare($sql2);
        $stmtm->bindParam(':email', $email);
        $stmtm->execute();
        $userDatamst = $stmtm->fetch(PDO::FETCH_ASSOC);

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

            // Ajouter image n PDF
            $pdf->Image($tempFile, 160, 60, $width, $height, $imageType);
            
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

                //Logos
                $pdf->Image('fst-1024x383.png', 10, 10, 50);
                $pdf->Image('logo-uae-1024x297.png', 150, 10, 50);

                // Title
                $pdf->SetFont('Arial', 'B', 16);
                $pdf->Ln(30);
                $pdf->Cell(0, 10, ("Fiche d'inscription au Cycle d'ingenieurs"), 0, 1, 'C');

                //image
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
        }elseif($userDatamst){
            if(isset($_POST['submit'])){
                require 'vendor/setasign/fpdf/fpdf.php'; //hadi l library dyal pdf
            
                $pdf = new FPDF();
                $pdf->AddPage(); //hadchi dyal creation d'une page
            
                // logos
                $pdf->Image('fst-1024x383.png', 10, 10, 50);
                $pdf->Image('logo-uae-1024x297.png', 150, 10, 50);

                // 3onwan
                $pdf->SetFont('Arial', 'B', 16);
                $pdf->Ln(30);
                $pdf->Cell(0, 10, ("Fiche d'inscription au Cycle Master"), 0, 1, 'C');

                // tsawer
                if (displayImageInPDF($pdf, $userDatamst['user_image'])) {
                    $pdf->Ln(5);
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

                // lqa3
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
        }else{
        throw new Exception("No registration data found");
        
        }
    }
}catch(Exception $e){
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
        <?php elseif($userDataci || $userDatamst): ?>
            <h1>Formulaire d'inscription</h1>
            <div class="download-form">
                <input type="submit" name="submit" value="Download">
            </div>
        <?php endif; ?>
    </div>
    </form>
</body>
</html>