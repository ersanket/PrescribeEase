<?php
require_once('vendor/autoload.php');
use setasign\Fpdi\Tcpdf\Fpdi;

// Database connection
$servername = "localhost";
$username = "doctor_user";
$password = "password";
$dbname = "doctor_portal";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$name = isset($_POST['name']) ? $_POST['name'] : '';
$age = isset($_POST['age']) ? $_POST['age'] : '';
$gender = isset($_POST['gender']) ? $_POST['gender'] : '';
$contact = isset($_POST['contact']) ? $_POST['contact'] : '';
$template = isset($_POST['template']) ? $_POST['template'] : '';
$content = isset($_POST['content']) ? $_POST['content'] : '';

// Insert patient details
$sql = "INSERT INTO patients (name, age, gender, contact) VALUES ('$name', '$age', '$gender', '$contact')";
if ($conn->query($sql) === TRUE) {
    $patient_id = $conn->insert_id;
} else {
    die("Error: " . $sql . "<br>" . $conn->error);
}

// Insert template content
$sql = "INSERT INTO templates (patient_id, template_type, content) VALUES ($patient_id, '$template', '$content')";
if ($conn->query($sql) !== TRUE) {
    die("Error: " . $sql . "<br>" . $conn->error);
}

$conn->close();

// Generate PDF
$pdf = new Fpdi();
$pdf->AddPage();
$pdf->setSourceFile('Discharge_Template.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0, 210);

// Add content to the template
$pdf->SetFont('helvetica', '', 12);

if ($template == 'medicalcertificate') {
    $pdf->SetXY(30, 60); // Adjust the position as needed
    $pdf->MultiCell(0, 10, "IP/Mr. No: " . (isset($_POST['ip_mr_no']) ? $_POST['ip_mr_no'] : ''), 0, 'L', 0, 1);
    $pdf->SetXY(30, 70);
    $pdf->MultiCell(0, 10, "Patient ID: " . (isset($_POST['patient_id']) ? $_POST['patient_id'] : ''), 0, 'L', 0, 1);
    $pdf->SetXY(30, 80);
    $pdf->MultiCell(0, 10, "Name: " . $name, 0, 'L', 0, 1);
    $pdf->SetXY(30, 90);
    $pdf->MultiCell(0, 10, "Age/Sex: " . $age . "/" . $gender, 0, 'L', 0, 1);
    $pdf->SetXY(30, 100);
    $pdf->MultiCell(0, 10, "Ward/Bed: " . (isset($_POST['ward_bed']) ? $_POST['ward_bed'] : ''), 0, 'L', 0, 1);
    $pdf->SetXY(30, 110);
    $pdf->MultiCell(0, 10, "Date Of Operation: " . (isset($_POST['date_of_operation']) ? $_POST['date_of_operation'] : ''), 0, 'L', 0, 1);
    $pdf->SetXY(30, 120);
    $pdf->MultiCell(0, 10, "Date Of Admission: " . (isset($_POST['date_of_admission']) ? $_POST['date_of_admission'] : ''), 0, 'L', 0, 1);
    $pdf->SetXY(30, 130);
    $pdf->MultiCell(0, 10, "Date Of Discharge: " . (isset($_POST['date_of_discharge']) ? $_POST['date_of_discharge'] : ''), 0, 'L', 0, 1);
    $pdf->SetXY(30, 140);
    $pdf->MultiCell(0, 10, "Doctor’s Name: " . (isset($_POST['doctor_name']) ? $_POST['doctor_name'] : ''), 0, 'L', 0, 1);
    $pdf->SetXY(30, 150);
    $pdf->MultiCell(0, 10, "Diagnosis/Remark: " . (isset($_POST['diagnosis_remark']) ? $_POST['diagnosis_remark'] : ''), 0, 'L', 0, 1);
    $pdf->SetXY(30, 160);
    $pdf->MultiCell(0, 10, "Complaints: " . (isset($_POST['complaints']) ? $_POST['complaints'] : ''), 0, 'L', 0, 1);
    $pdf->SetXY(30, 170);
    $pdf->MultiCell(0, 10, "Vitals: " . (isset($_POST['vitals']) ? $_POST['vitals'] : ''), 0, 'L', 0, 1);
    $pdf->SetXY(30, 180);
    $pdf->MultiCell(0, 10, "Treatment Given: " . (isset($_POST['treatment_given']) ? $_POST['treatment_given'] : ''), 0, 'L', 0, 1);
    $pdf->SetXY(30, 190);
    $pdf->MultiCell(0, 10, "Condition During Discharge: " . (isset($_POST['condition_during_discharge']) ? $_POST['condition_during_discharge'] : ''), 0, 'L', 0, 1);
    $pdf->SetXY(30, 200);
    $pdf->MultiCell(0, 10, "Advice On Discharge: " . (isset($_POST['advice_on_discharge']) ? $_POST['advice_on_discharge'] : ''), 0, 'L', 0, 1);
}

$pdf->Output('document.pdf', 'D');
?>

