<?php 
    session_start();
    require_once 'db_connection.php';

    require 'vendor/autoload.php';
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    use PhpOffice\PhpSpreadsheet\IOFactory;


    $uploadError = null;
function updateExcelFiles($studentData,$choix, $program, $moyen1) {
    // kadir directory bach it7to les fichier
    $dir = 'concour';
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }

    // katsmi excel ela hsab filiere
    $filename = $dir . '/' . strtolower($program) . '_ci_students.xlsx';

    // tqaleb ela excel qdim wla t creer wahed jdid
    if (file_exists(filename: $filename)) {
        $spreadsheet = IOFactory::load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        $row = $sheet->getHighestRow() + 1;
    } else {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Full Name', 'CIN', 'CNE', 'Email', 'Phone','Filiere', 'Choix', 'Note DEUST/DEUG/DUT'];
        $sheet->fromArray([$headers], NULL, 'A1');
        $row = 2;
    }

    // data dyal student
    $data = [
        $studentData['fullname'],
        $studentData['cin'],
        $studentData['cne'],
        $studentData['email'],
        '0' . $studentData['telephone'],
        $choix,
        $program,
        $moyen1
    ];
    
    $sheet->fromArray([$data], NULL, 'A' . $row);

    // sejel fichiers
    $writer = new Xlsx($spreadsheet);
    $writer->save($filename);
}
    $error = null;
    

     if(isset($_SESSION['email'])) {
        $email = $_SESSION['email'];
        $sql = "SELECT * FROM student WHERE email = :email";
        $stmt = $dsn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if(!$userData) {
            throw new Exception("No registration data found");
        }

    } else {
        throw new Exception("Please complete registration first");
    }

    if(isset($_POST["submit"])){
        if(!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
            $uploadError = "Please upload your photo";
        } else {
            $image = $_FILES['image'];
            $imageName = $image['name'];
            $imageType = $image['type'];
            $imageTmpName = $image['tmp_name'];
            $imageError = $image['error'];
            
            // katchoflek wach l'image dayza (jpg/png)
            $allowedTypes = ['image/jpeg', 'image/png'];
            if(!in_array($imageType, $allowedTypes)) {
                $uploadError = "Only JPG and PNG images are allowed";
            } else if($imageError !== UPLOAD_ERR_OK) {
                $uploadError = "Error uploading image";
            } else {
                $imageContent = file_get_contents($imageTmpName);
            }
        }
        if(!isset($_FILES['upload']) || $_FILES['upload']['error'] === UPLOAD_ERR_NO_FILE) {
            $uploadError = "Please upload your PDF file before submitting";
        } else {
            $file = $_FILES['upload'];
            $fileName = $file['name'];
            $fileType = $file['type'];
            $fileTmpName = $file['tmp_name'];
            $fileError = $file['error'];
            $stmt1 = $dsn->prepare("SELECT * FROM cycle WHERE email = :email");
            $stmt1->bindParam(':email', $email);
            $stmt1->execute();
            $stmt1->fetchAll();
            if($stmt1->rowCount() > 0){
                header("Location: download.php");
            }else{
                if($fileType !== 'application/pdf') {
                    $uploadError = "Only PDF files are allowed";
                } else if($fileError !== UPLOAD_ERR_OK) {
                    $uploadError = "Error uploading file";
                } else {
                    $fileContent = file_get_contents($fileTmpName);
                    $maxFileSize = 5 * 1024 * 1024;
            if (strlen($fileContent) > $maxFileSize) {
                $uploadError = "File size exceeds maximum limit of 5MB";
                return;
            }
                    $choix = $_POST['deust/deug'];
                    $moyen1 = $_POST['note']; 
                    $classement = $_POST['ci'];
                try {
                    $dsn->beginTransaction();
                    
                    $stmt2 = $dsn->prepare("INSERT INTO cycle (fullname, cin, cne, ddn, genre, telephone, 
                        ldn, nationalite, annee, serie, Mention, email, filiere, moyen1, classement, 
                        pdf_file, file_name, user_image) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
                    $stmt2->execute([
                        $userData['fullname'], 
                        $userData['cin'], 
                        $userData['cne'], 
                        $userData['ddn'], 
                        $userData['genre'], 
                        $userData['telephone'],
                        $userData['ldn'], 
                        $userData['nationalite'], 
                        $userData['annee'], 
                        $userData['serie'], 
                        $userData['Mention'], 
                        $userData['email'], 
                        $userData['filiere'], 
                        $moyen1, 
                        $classement,
                        $fileContent,
                        $fileName,
                        $imageContent
                    ]);
    
                    updateExcelFiles($userData, $choix, $classement, $moyen1);
                    $dsn->commit();
                    $success = "Data updated successfully";
                    header("Location: download.php");
                    exit();
                } catch(PDOException $e) {
                    if ($dsn->inTransaction()) {
                        $dsn->rollBack();
                    }
                    $uploadError = "Error saving to database: " . $e->getMessage();
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://fstt.ac.ma/Portail2023/wp-content/uploads/2023/03/Untitled-3-300x300.png" sizes="192x192">
    <title>Inscription au CD en FSTT</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <ul class="logos">
        <li class="fst-logo">
            <img src="fst-1024x383.png" alt="FSTTLogo" style="width: 230px; height: 80px;">
        </li>
        <li class="uni-logo">
            <img src="logo-uae-1024x297.png" alt="UniLogo" style="width: 230px; height: 60px;">
        </li>
    </ul>
<form action="" method="post" enctype="multipart/form-data">
    <div class="form-section" style="display: flex; justify-content: center; align-items: center; height: 100vh;">
        <div class="section" style="text-align: center; width: 400px; height: 336px; margin-right: 25px;">
            <h2>Inscription au cycle d'ingenieur</h2>
            <p><?php echo $userData['fullname']; ?></p>
            <input type="number" name="note" placeholder="Moyen de 2 Année preparatoire" step="0.01" min="10.00" max="19.99" required>
            <select name="deust/deug">
                <option value="">Selectionner votre specialité</option>
                <option value="CB">Chimie/Bio</option>
                <option value="MSD">Math/Science des données</option>
                <option value="I">Informatique</option>
                <option value="P">Physique</option>
            </select>
    
    
            <select size="1" name="ci" id="ci">
                <option value="">Selectionner votre preference</option>
                <option value="LSI">Logiciels et systèmes Intelligens</option>
                <option value="GI">Genie industriel</option>
                <option value="GEO">Geoinformation</option>
                <option value="GEMI">Genie Electrique et Management Industriel</option>
                <option value="GA">Genie Agroalimentaire</option>
            </select>
    </div>
<div class="section" style="text-align: center; width: 400px; height: auto; margin-left: 25px;">
        <div class="input-group">
            <p>Ajouter votre photo (JPG/PNG) :<br><B>la taille max 1MB</B></p>
            <input type="file" name="image" id="image" accept="image/jpeg,image/png">
            <label for="image"><span>No image chosen</span></label>
        </div>
        
        <div class="input-group">
        <p>Mettre votre fichier ici (sous forme <I>pdf</I> ) :<br><B>la taille max 5MB</B></p>
            <input type="file" name="upload" id="upload" accept=".pdf">
            <label for="upload"><span>No file chosen</span></label>
        </div>
            <input type="submit" name="submit" value="Finish">
            <p style="color: red;"><?php echo $uploadError; ?></p>
        </div>
    </div>
</form>
</body>
</html>
<script>
    document.querySelectorAll('input[type="file"]').forEach(input => {
    input.addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name || 'No file chosen';
        const label = e.target.nextElementSibling;
        const span = label.querySelector('span') || document.createElement('span');
        span.textContent = fileName;
        if (!label.querySelector('span')) {
            label.appendChild(span);
        }
    });
});
    document.querySelector('form').addEventListener('submit', function(e) {
    const fileInput = document.querySelector('input[type="file"]');
    if (!fileInput.files.length) {
        e.preventDefault();
        alert('Please select a PDF file before submitting');
    }
});
    document.addEventListener('DOMContentLoaded', function() {
    const dSelect = document.querySelector('select:first-of-type');
    const ciSelect = document.querySelector('select:last-of-type');

    dSelect.addEventListener('change', function() {
        ciSelect.hidden = false;
        // dkshi ghadi ykon hidden flbdya
        Array.from(ciSelect.options).forEach(option => {
            option.hidden = true;
        });
        // aybano filieres ela hsab deust/deug/dut dyalk
        ciSelect.options[0].hidden = false;
        switch(this.value) {
            case 'P':
                Array.from(ciSelect.options).filter(opt => 
                    ['GI', 'GEMI'].includes(opt.value)
                ).forEach(opt => opt.hidden = false);
                break;
            case 'MSD':
            case 'I':
                Array.from(ciSelect.options).filter(opt => 
                    ['LSI', 'GEO'].includes(opt.value)
                ).forEach(opt => opt.hidden = false);
                break;
            case 'CB':
                Array.from(ciSelect.options).find(opt => 
                    opt.value === 'GA'
                ).hidden = false;
                break;
        }

        ciSelect.selectedIndex = 0;
    });
    
});
</script>