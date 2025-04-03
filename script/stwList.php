<?php
include_once('../inc/global.php');
include_once("../config/route.php");
$user = unserialize($_SESSION['user']);
$today = Utility\WebSystem::displayDate(date("Y-m-d H:i:s"), 'Y-m-d');


$map = [
    'column_stwID' => 'stw.id',
    'column_refNo' => 'stw.refNo',
    'column_tpbNo' => 'tpb.TPBNo',
    'column_client' => 'client.contactPerson',
    'column_addressDDLot' => 'stw.addressDDLot',
    'column_submissionDate' => 'stw.submissionDate',
    'column_status' => 'generalStatus.name',
    'column_function' => 'stw.id'
];

$tpbID = $_GET['tpbID'];
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = $_POST['search']['value']; // Search value
$searchQuery = " ";


$sqlAll = Database\Sql::select(['stw', 'stw'])        
->leftJoin(['generalStatus', 'generalStatus'], "stw.status = generalStatus.id")
->leftJoin(['tpb', 'tpb'], "stw.tpbID = tpb.id")
->leftJoin(['client', 'client'], "stw.clientID = client.id");

if(!empty($tpbID)){
    $sqlAll->where(['stw.tpbID', '=', $tpbID]);
}

$sqlAll->setFieldValue('
   stw.id stwID, 
   stw.refNo stwRefNo, 
   tpb.TPBNo tpbNo, 
   client.contactPerson contactPerson, 
   stw.addressDDLot addressDDLot, 
   stw.submissionDate submissionDate, 
   generalStatus.name statusName                         
');

if($searchValue != ''){
    $searchValue = addslashes($searchValue);
     $sqlAll->whereOp("(stw.id LIKE '%".$searchValue."%' 
        OR stw.refNo LIKE '%".$searchValue."%'
        OR tpb.TPBNo LIKE '%".$searchValue."%'
        OR client.contactPerson LIKE '%".$searchValue."%'
        OR stw.addressDDLot LIKE '%".$searchValue."%'
        OR stw.submissionDate LIKE '%".$searchValue."%'
        OR generalStatus.name LIKE '%".$searchValue."%'
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
    $sqlAll = Database\Sql::select(['stw', 'stw'])        
    ->leftJoin(['generalStatus', 'generalStatus'], "stw.status = generalStatus.id")
    ->leftJoin(['tpb', 'tpb'], "stw.tpbID = tpb.id")
    ->leftJoin(['client', 'client'], "stw.clientID = client.id");

    if(!empty($tpbID)){
        $sqlAll->where(['stw.tpbID', '=', $tpbID]);
    }

    $sqlAll->setFieldValue('
       stw.id stwID, 
       stw.refNo stwRefNo, 
       tpb.TPBNo tpbNo, 
       client.contactPerson contactPerson, 
       stw.addressDDLot addressDDLot, 
       stw.submissionDate submissionDate, 
       generalStatus.name statusName                         
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
            "column_stwID"=>$data['stwID'],
            "column_refNo"=>$data['stwRefNo'],
            "column_tpbNo"=>$data['tpbNo'],
            "column_client"=>$data['contactPerson'],
            "column_addressDDLot"=>$data['addressDDLot'],
            "column_submissionDate"=>($data['submissionDate']=="0000-00-00"?" ":$data['submissionDate']),   
            "column_status"=>$data['statusName'],        
            "column_function"=>"
                <div class='btn-group' role='group' aria-label=''>
                    <div class='btn-group' role='group'>
                        <button id='btnGroupDrop".$data['stwID']."' type='button' class='btn btn-outline-dark dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false'>
                            ".L('Actions')."
                        </button>
                    <ul class='dropdown-menu' aria-labelledby='btnGroupDrop".$data['stwID']."'>
                        <li>
                            <div class='d-grid'>
                                <button class='btn btn-md btn-outline-dark btnView' type='button' data-id='".$data['stwID']."'><i class='fas fa-sm fa-eye'></i> ".L('View')."</button>    
                                <button class='btn btn-md btn-outline-dark btnEdit' type='button' data-id='".$data['stwID']."'><i class='fas fa-sm fa-edit'></i> ".L('Edit')."</button>
                                <button class='btn btn-md btn-outline-dark btnDel' type='button' data-id='".$data['stwID']."'><i class='fas fa-sm fa-trash-alt'></i> ".L('Delete')."</button>
                            </div>
                        </li>
                    </ul>
                    </div>
                </div>
            "
        ];    
    } else {
        $dataArr = [
            "column_stwID"=>$data['stwID'],
            "column_refNo"=>$data['stwRefNo'],
            "column_tpbNo"=>$data['tpbNo'],
            "column_client"=>$data['contactPerson'],
            "column_addressDDLot"=>$data['addressDDLot'],
            "column_submissionDate"=>($data['submissionDate']=="0000-00-00"?" ":$data['submissionDate']),   
            "column_status"=>$data['statusName'],        
            "column_function"=>"
                <div class='btn-group' role='group' aria-label=''>
                    <div class='btn-group' role='group'>
                        <button id='btnGroupDrop".$data['stwID']."' type='button' class='btn btn-outline-dark dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false'>
                            ".L('Actions')."
                        </button>
                    <ul class='dropdown-menu' aria-labelledby='btnGroupDrop".$data['stwID']."'>
                        <li>
                            <div class='d-grid'>
                                <button class='btn btn-md btn-outline-dark btnView' type='button' data-id='".$data['stwID']."'><i class='fas fa-sm fa-eye'></i> ".L('View')."</button>                                   
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