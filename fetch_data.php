<?php
header('Content-Type: application/json');

// Database connection information
$serverName = "0868\SQLEXPRESS01";
$database = "CtData";
$uid = "cypersoft";
$pass = "Deep@0868";
    
$connection = [
    "Database" => $database,
    "Uid" => $uid,
    "PWD" => $pass
];

// Establish the connection
$conn = sqlsrv_connect($serverName, $connection);

if (!$conn) {
    die(json_encode(["error" => sqlsrv_errors()]));
}

// Get the design name from the query string
$designname = isset($_GET['designname']) ? $_GET['designname'] : null;

// SQL query with parameterized input for filtering by design name
$tsql = "SELECT 
    dbo.DesignName.DesignName, 
    MIN(dbo.DesignName.DesignNameC) AS FirstOfDesignNameC, 
    dbo.PrdtName.PrdtName, 
    dbo.BrandName.BrandName, 
    dbo.SizeName.SizeName, 
    dbo.CatName.CatName, 
    dbo.Batch.BtName, 
    dbo.Shade.ShName, 
    dbo.MfgStatus.MsName, 
    MIN(dbo.DesignName.DesignAct) AS FirstOfDesignAct, 
    SUM(dbo.SubDesign.G1) AS SumOfG1, 
    SUM(dbo.SubDesign.G2) AS SumOfG2, 
    SUM(dbo.SubDesign.G3) AS SumOfG3, 
    SUM(dbo.SubDesign.G4) AS SumOfG4, 
    SUM(dbo.SubDesign.Gtot) AS SumOfGtot, 
    MIN(dbo.SubDesign.BtBoxWt) AS FirstOfBtBoxWt
FROM 
    dbo.DesignName
    INNER JOIN dbo.PrdtName ON dbo.DesignName.PrdtCode = dbo.PrdtName.PrdtCode
    INNER JOIN dbo.BrandName ON dbo.DesignName.BrandCode = dbo.BrandName.BrandCode
    INNER JOIN dbo.SizeName ON dbo.DesignName.SizeCode = dbo.SizeName.SizeCode
    INNER JOIN dbo.CatName ON dbo.DesignName.CatCode = dbo.CatName.CatCode
    INNER JOIN dbo.SubDesign ON dbo.DesignName.GenCode = dbo.SubDesign.GenCode
    INNER JOIN dbo.Batch ON dbo.SubDesign.BtCode = dbo.Batch.BtCode
    INNER JOIN dbo.Shade ON dbo.SubDesign.ShCode = dbo.Shade.ShCode
    INNER JOIN dbo.MfgStatus ON dbo.SubDesign.MsCode = dbo.MfgStatus.MsCode
    INNER JOIN dbo.Concept ON dbo.DesignName.CptCode = dbo.Concept.CptCode
    INNER JOIN dbo.BPName ON dbo.DesignName.BPCode = dbo.BPName.BPCode
    INNER JOIN dbo.FinishGlaze ON dbo.DesignName.FGCode = dbo.FinishGlaze.FGCode
" . ($designname ? "WHERE dbo.DesignName.DesignName = ? " : "") . "
GROUP BY 
    dbo.DesignName.DesignName, 
    dbo.PrdtName.PrdtName, 
    dbo.BrandName.BrandName, 
    dbo.SizeName.SizeName, 
    dbo.CatName.CatName, 
    dbo.Batch.BtName, 
    dbo.Shade.ShName, 
    dbo.MfgStatus.MsName
HAVING 
    SUM(dbo.SubDesign.Gtot) <> 0"; // Exclude rows where SumOfGtot is zero

// Prepare parameters only if filtering by design name
$params = $designname ? [$designname] : [];

// Prepare and execute the SQL statement
$stmt = sqlsrv_query($conn, $tsql, $params);

if ($stmt === false) {
    die(json_encode(["error" => sqlsrv_errors()]));
}

$data = [];

// Fetch data and populate the array
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $data[] = $row;
}

// Free the statement and close the connection
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

// Return the data in JSON format
echo json_encode($data);
?>
