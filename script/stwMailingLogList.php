<?php
include_once('../inc/global.php');
include_once("../config/route.php");
$user = unserialize($_SESSION['user']);
$today = Utility\WebSystem::displayDate(date("Y-m-d H:i:s"), 'Y-m-d');

$map = [
    'column_mailingLogID' => 'log.id',
    'column_date' => 'log.mailingDate',
    'column_from' => 'log.mailingFrom',
    'column_content' => 'log.mailingContent',
    'column_function' => 'conditions.id'
];

$stwID = $_GET['stwID'];
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = $_POST['search']['value']; // Search value
$searchQuery = " ";


$sqlAll = Database\Sql::select(['stwMailingLog', 'log']);


if(!empty($stwID)){
    $sqlAll->where(['log.stwID', '=', $stwID]);
}

$sqlAll->setFieldValue('
    log.id logID, 
    log.stwID stwID,
    log.mailingDate mailingDate, 
    log.mailingFrom mailingFrom, 
    log.mailingContent mailingContent             
');

if($searchValue != ''){
    $searchValue = addslashes($searchValue);
     $sqlAll->whereOp("(log.id LIKE '%".$searchValue."%'        
        OR log.mailingDate LIKE '%".$searchValue."%'
        OR log.mailingFrom LIKE '%".$searchValue."%'
        OR log.mailingContent LIKE '%".$searchValue."%'
    )");
}

foreach($_POST['columns'] as $idx => $column){
    if(!empty($column['search']['value'])) {
        $idxSearchValue = substr($column['search']['value'], 1, -1);
        if($_POST['columns'][$idx]['data']=="column_status"){
           if($idxSearchValue=="有效") {
             $idxSearchValue="Enabled";
           }

           if($idxSearchValue=="無效") {
             $idxSearchValue="Disabled";
           }           
        } 

        $sqlAll->where([$map[$_POST['columns'][$idx]['data']], '=', '"'.strip_tags($idxSearchValue).'"']);
    }
}

$stmAll = $sqlAll->prepare();
$stmAll->execute();

if($stmAll->rowCount()==0 && $filter) {
    $sqlAll = Database\Sql::select(['stwMailingLog', 'log']);

    if(!empty($stwID)){
        $sqlAll->where(['log.stwID', '=', $stwID]);
    }
    
    $sqlAll->setFieldValue('
        log.id logID, 
        log.stwID stwID,
        log.mailingDate mailingDate, 
        log.mailingFrom mailingFrom, 
        log.mailingContent mailingContent             
    ');
};

$sql = $sqlAll->order($map[$columnName],$columnSortOrder)->limit($rowperpage, $row);

$stm = $sql->prepare();
$stm->execute();
$returnArr = [];
$contentArr = [];
$lineCount = 0;
foreach($stm as $data){    
    if(@$_GET['mode']=="edit") {
        $dataArr = [
            "column_mailingLogID"=>$data['logID'],
            "column_date"=>$data['mailingDate'],
            "column_from"=>$data['mailingFrom'],
            "column_content"=>$data['mailingContent'],       
            "column_function"=>"
                <div class='btn-group' role='group' aria-label=''>
                    <div class='btn-group' role='group'>
                        <button id='btnGroupDrop".$data['logID']."' type='button' class='btn btn-outline-dark dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false'>
                            ".L('Actions')."
                        </button>
                    <ul class='dropdown-menu' aria-labelledby='btnGroupDrop".$data['logID']."'>
                        <li>
                            <div class='d-grid'>
                                <button class='btn btn-md btn-outline-dark btnView' type='button' data-id='".$data['logID']."' data-stwid='".$data['stwID']."'><i class='fas fa-sm fa-eye'></i> ".L('View')."</button>                                
                                <button class='btn btn-md btn-outline-dark btnEdit' type='button' data-id='".$data['logID']."' data-stwid='".$data['stwID']."'><i class='fas fa-sm fa-edit'></i> ".L('Edit')."</button>
                                <button class='btn btn-md btn-outline-dark btnDel' type='button' data-id='".$data['logID']."'><i class='fas fa-sm fa-trash-alt'></i> ".L('Delete')."</button>
                            </div>
                        </li>
                    </ul>
                    </div>
                </div>
            "
        ];   
    } else {
        $dataArr = [
            "column_mailingLogID"=>$data['logID'],
            "column_date"=>$data['mailingDate'],
            "column_from"=>$data['mailingFrom'],
            "column_content"=>$data['mailingContent'],       
            "column_function"=>"
                <div class='btn-group' role='group' aria-label=''>
                    <div class='btn-group' role='group'>
                        <button id='btnGroupDrop".$data['logID']."' type='button' class='btn btn-outline-dark dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false'>
                            ".L('Actions')."
                        </button>
                    <ul class='dropdown-menu' aria-labelledby='btnGroupDrop".$data['logID']."'>
                        <li>
                            <div class='d-grid'>
                                <button class='btn btn-md btn-outline-dark btnView' type='button' data-id='".$data['logID']."' data-stwid='".$data['stwID']."'><i class='fas fa-sm fa-eye'></i> ".L('View')."</button>                                
                            </div>
                        </li>
                    </ul>
                    </div>
                </div>
            "
        ];          
    } 

    $lineCount++;
    $contentArr[] = $dataArr;
}

$returnArr['draw'] = intval($draw);
$returnArr["iTotalDisplayRecords"] = $stmAll->rowCount();
$returnArr["iTotalRecords"] = $stmAll->rowCount();
$returnArr["data"] = $contentArr;

echo json_encode($returnArr);

?>