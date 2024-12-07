<?php 
    session_start();
    require_once 'db_connection.php';
    
    require 'vendor/autoload.php';
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    use PhpOffice\PhpSpreadsheet\IOFactory;

    $uploadError = null;
    function updateExcelFiles($studentData, $program, $moyen1, $moyen2) {
    // Create concour directory if it doesn't exist
    $dir = 'concour';
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }

    // katkhdm ela fichier li khtar utilisateur
    $filename = $dir . '/' . strtolower($program) . '_mst_students.xlsx';

    // katrje3 l fichier excel wla katcréé wahed jdid
    if (file_exists(filename: $filename)) {
        $spreadsheet = IOFactory::load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        $row = $sheet->getHighestRow() + 1;
    } else {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // l'en tête de fichier excel
        $headers = ['Full Name', 'CIN', 'CNE', 'Email', 'Phone', 'Choix', 'Note DEUST/DEUG/DUT', 'Note LST'];
        $sheet->fromArray([$headers], NULL, 'A1');
        $row = 2;
    }

    //new student data
    $data = [
        $studentData['fullname'],
        $studentData['cin'],
        $studentData['cne'],
        $studentData['email'],
        '0' . $studentData['telephone'],
        $program,
        $moyen1, 
        $moyen2
    ];
    
    $sheet->fromArray([$data], NULL, 'A' . $row);

    // nsjlo fichier
    $writer = new Xlsx($spreadsheet);
    $writer->save($filename);
}

    $error = null;

    if(isset($_SESSION['email'])) {
        $email = $_SESSION['email'];
        $sql = "SELECT * FROM student WHERE email = :email";
        $stmtmst = $dsn->prepare($sql);
        $stmtmst->bindParam(':email', $email);
        $stmtmst->execute();
        $userData4 = $stmtmst->fetch(PDO::FETCH_ASSOC);

    if(!$userData4) {
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
            
            // katchof wach limage li dkhlha eandha lformat li kayn f $allowedTypes
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
        $stmt1 = $dsn->prepare("SELECT * FROM master WHERE email = :email");
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
        $moyen1 = $_POST['note1'];
        $moyen2 = $_POST['note2'];
        $classement = $_POST['mst'];
        try {
            $dsn->beginTransaction();
            
            // Remove dossier_number query and use direct insert
            $stmt = $dsn->prepare("INSERT INTO master (fullname, cin, cne, ddn, genre, telephone, 
                ldn, nationalite, annee, serie, Mention, email, filiere, moyen1, moyen2, 
                classement, pdf_file, file_name, user_image) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->execute([
                $userData4['fullname'], 
                $userData4['cin'], 
                $userData4['cne'], 
                $userData4['ddn'], 
                $userData4['genre'], 
                $userData4['telephone'],
                $userData4['ldn'], 
                $userData4['nationalite'], 
                $userData4['annee'], 
                $userData4['serie'], 
                $userData4['Mention'], 
                $userData4['email'], 
                $userData4['filiere'], 
                $moyen1,
                $moyen2,
                $classement,
                $fileContent,
                $fileName,
                $imageContent
            ]);
        updateExcelFiles($userData4, $classement, $moyen1, $moyen2);
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
    <title>Inscription au MST</title>
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
        <div class="section" style="text-align: center; width: 400px; height: 352px; margin-right: 25px;">
            <h2>Master Registration Form</h2>
            <p>Full Name: <?php echo $userData4['fullname']; ?></p>
            <input type="number" name="note1" step="0.01" placeholder="Moyen de 2 Année preparatoire" min="10.00" max="19.99" required>
            <input type="number" name="note2" step="0.01" placeholder="Moyen de LST" min="10.00" max="19.99" required>
            <select name="lst">
                <option value="">Select LST</option>
                <option value="LSTCB">LST en Chimie/Bio</option>
                <option value="LSTM">LST en Math</option>
                <option value="LSTI">LST en Informatique</option>
                <option value="LSTGC">LST en Genie Civil</option>
                <option value="LSTENR">LST en Energitique</option>
            </select>
            <select size="1" name="mst" id="mst">
                <option value="">Select MST</option>
                <option value="SE" hidden>Sciences d'Environnement</option>
                <option value="AISD" hidden>Intelligence Artificielle et Sciences de Données</option>
                <option value="ITBD" hidden>IT et Big Data</option>
                <option value="GC" hidden>Genie Civil</option>
                <option value="GE" hidden>Genie Energitique</option>
                <option value="MMSD" hidden>Modélisation Mathématique et Science de Données</option>
            </select>
        </div>
        <div class="section" style="text-align: center; width: 400px; height: auto; margin-left: 25px;">
            <div class="input-group">
            <p>Ajouter votre photo (JPG/PNG) :</p>
            <p><B>la taille ne depasse pas 1MB</B></p>
            <input type="file" name="image" id="image" accept="image/jpeg,image/png">
            <label for="image"><span>No image chosen</span></label>
        </div>
        
        <div class="input-group">
            <p>Mettre votre fichier ici (sous forme <I>pdf</I> ) :<br><B>la taille ne depasse pas 5MB</B></p>
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
    document.addEventListener('DOMContentLoaded', function() {
    const lstSelect = document.querySelector('select:first-of-type');
    const mstSelect = document.querySelector('select:last-of-type');

    lstSelect.addEventListener('change', function() {
        mstSelect.hidden = false;
        // dkshi ghadi ykon hidden flbdya
        Array.from(mstSelect.options).forEach(option => {
            option.hidden = true;
        });
        // aybano filieres ela hsab licence dyalk
        mstSelect.options[0].hidden = false;
        switch(this.value) {
            case 'LSTCB':
                Array.from(mstSelect.options).filter(opt => 
                    ['SE'].includes(opt.value)
                ).forEach(opt => opt.hidden = false); 
                break;
            case 'LSTM':
                Array.from(mstSelect.options).filter(opt => 
                    ['AISD', 'MMSD'].includes(opt.value)
                ).forEach(opt => opt.hidden = false);                
                break;
            case 'LSTI':
                Array.from(mstSelect.options).filter(opt => 
                    ['AISD', 'MMSD','ITBD'].includes(opt.value)
                ).forEach(opt => opt.hidden = false); 
                break;
            case 'LSTGC':
                Array.from(mstSelect.options).filter(opt => 
                    ['GC'].includes(opt.value)
                ).forEach(opt => opt.hidden = false); 
                break;
            case 'LSTENR':
                Array.from(mstSelect.options).filter(opt => 
                    ['GE'].includes(opt.value)
                ).forEach(opt => opt.hidden = false);
                break;
        }

        mstSelect.selectedIndex = 0;
        
    });
});
document.querySelectorAll('input[type="file"]').forEach(input => {
    input.addEventListener('change', function(e) {
        const parent = this.closest('.input-group');
        if (this.files.length > 0) {
            parent.classList.add('file-selected');
            parent.classList.remove('error');
        } else {
            parent.classList.remove('file-selected');
        }
    });
});
</script>


