<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
include_once('../inc/global.php');
include_once("../config/route.php");
$user = unserialize($_SESSION['user']);
$today = Utility\WebSystem::displayDate(date("Y-m-d H:i:s"), 'Y-m-d');


$map = [
    'column_tpbID' => 'tpb.id',
    'column_tpbRefNo' => 'tpb.refNo',
    'column_tpbClient' => 'clients.contactPerson',
    'column_tpbOfficer' => 'GROUP_CONCAT(" ", officer.username)',
    'column_tpbSubmissionDate' => 'tpb.submissionDate',
    'column_tpbLastUpdateDate' => 'tpb.lastUpdateDate',
    'column_tpbNo' => 'tpb.TPBNo',
    'column_function' => 'tpb.id'
];

$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = $_POST['search']['value']; // Search value
$searchQuery = " ";

$sqlAll = Database\Sql::select(['tpb', 'tpb'])
        ->leftJoin(['tpbStatus', 'tpbStatus'], "tpb.tpbStatusID = tpbStatus.id")
        ->leftJoin(['client', 'clients'], "tpb.clientID = clients.id")
        ->leftJoin(['tpbOfficer', 'tpbOfficer'], "tpb.id = tpbOfficer.tpbID")
        ->leftJoin(['user', 'officer'], "officer.id = tpbOfficer.userID");

        if(!empty($_GET['type'])){
            $sqlAll->where(['tpb.tpbStatusID', '=', $_GET['type']]);
        }        
        
        $sqlAll->setFieldValue('
            tpb.id tpbID, 
            tpb.refNo refNo, 
            clients.contactPerson clientName, 
            tpbOfficer.userID userID, 
            tpb.submissionDate submissionDate, 
            tpb.lastUpdateDate lastUpdateDate, 
            tpb.TPBNo TPBNo, 
            tpbStatus.name statusName
        ');

if($searchValue != ''){
    $searchValue = addslashes($searchValue);
    $sqlAll->whereOp("(tpb.id LIKE '%".$searchValue."%' 
        OR tpb.refNo LIKE '%".$searchValue."%'
        OR clients.contactPerson LIKE '%".$searchValue."%'       
        OR tpb.submissionDate LIKE '%".$searchValue."%'
        OR tpb.lastUpdateDate LIKE '%".$searchValue."%'
        OR tpb.TPBNo LIKE '%".$searchValue."%' 
        OR officer.displayName LIKE '%".$searchValue."%' 
    )");
}


$filter = false;
foreach($_POST['columns'] as $idx => $column){
    if(!empty($column['search']['value'])) {
        $filter = true;
        $idxSearchValue = substr($column['search']['value'], 1, -1);
        if($_POST['columns'][$idx]['data']=="column_tpbStatus"){
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

$sqlAll->where(['1', 'GROUP BY', 'tpb.id']);

$stmAll = $sqlAll->prepare();
$stmAll->execute();

if($stmAll->rowCount()==0 && !$filter) {

    $sqlAll = Database\Sql::select(['tpb', 'tpb'])
    ->leftJoin(['tpbStatus', 'tpbStatus'], "tpb.tpbStatusID = tpbStatus.id")
    ->leftJoin(['client', 'clients'], "tpb.clientID = clients.id")
    ->leftJoin(['tpbOfficer', 'tpbOfficer'], "tpb.id = tpbOfficer.tpbID")
    ->leftJoin(['user', 'officer'], "officer.id = tpbOfficer.userID");

    if(!empty($_GET['type'])){
        $sqlAll->where(['tpb.tpbStatusID', '=', $_GET['type']]);
    }    
    
    $sqlAll->where(['1', 'GROUP BY', 'tpb.id']);
    
    $sqlAll->setFieldValue('
        tpb.id tpbID, 
        tpb.refNo refNo, 
        clients.contactPerson clientName, 
        tpbOfficer.userID userID, 
        tpb.submissionDate submissionDate, 
        tpb.lastUpdateDate lastUpdateDate, 
        tpb.TPBNo TPBNo, 
        tpbStatus.name statusName               
    ');

    
};

$sql = $sqlAll->order($map[$columnName],$columnSortOrder)->limit($rowperpage, $row);



$stm = $sql->prepare();
$stm->execute();

$returnArr = [];
$contentArr = [];
$lineCount = 0;
foreach($stm as $data){    

    $officeList = Controller\tpb::findOfficer($data['tpbID']);
    $arrOfficerName = [];
    foreach($officeList as $officer) {
        $arrOfficerName[] = Controller\user::find($officer['userID'])->displayName;
    }

    /*
    $dataArr = [
        "column_tpbID"=>$data['tpbID'],
        "column_tpbRefNo"=>$data['refNo'], 
        "column_tpbClient"=>$data['clientName'], 
        "column_tpbOfficer"=>implode(",", $arrOfficerName),
        "column_tpbSubmissionDate"=>$data['submissionDate'],   
        "column_tpbLastUpdateDate"=>$data['lastUpdateDate'],   
        "column_tpbNo"=>$data['TPBNo'],  

        "column_function"=>"
            <div class='btn-group' role='group' aria-label=''>
                <div class='btn-group' role='group'>
                    <button id='btnGroupDrop".$data['tpbID']."' type='button' class='btn btn-outline-dark dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false'>
                        ".L('Actions')."
                    </button>
                <ul class='dropdown-menu' aria-labelledby='btnGroupDrop".$data['tpbID']."'>
                    <li>
                        <div class='d-grid'>
                            <button class='btn btn-md btn-outline-dark btnEdit' type='button' data-id='".$data['tpbID']."'><i class='fas fa-sm fa-edit'></i> ".L('Edit')."</button>
                            <button class='btn btn-md btn-outline-dark btnDel' type='button' data-id='".$data['tpbID']."'><i class='fas fa-sm fa-trash-alt'></i> ".L('Delete')."</button>
                            <button class='btn btn-md btn-outline-dark btnFollowUp' type='button' data-id='".$data['tpbID']."'><i class='fas fa-sm fa-clipboard'></i> ".L('FollowUp')."</button>
                            <button class='btn btn-md btn-outline-dark btnReceive' type='button' data-id='".$data['tpbID']."'><i class='fas fa-sm fa-reply'></i> ".L('Receive')."</button>
                            <button class='btn btn-md btn-outline-dark btnDecision' type='button' data-id='".$data['tpbID']."'><i class='fas fa-sm fa-gavel'></i> ".L('Decision')."</button>
                            <button class='btn btn-md btn-outline-dark btnView' type='button' data-id='".$data['tpbID']."'><i class='fas fa-sm fa-eye'></i> ".L('View')."</button>
                        </div>
                    </li>
                </ul>
                </div>
            </div>
        "
    ];
    */   
    $dataArr = [
        "column_tpbID"=>$data['tpbID'],
        "column_tpbRefNo"=>$data['refNo'], 
        "column_tpbClient"=>$data['clientName'], 
        "column_tpbOfficer"=>implode(",", $arrOfficerName),
        "column_tpbSubmissionDate"=>($data['submissionDate']=="0000-00-00"?" ":$data['submissionDate']),   
        "column_tpbLastUpdateDate"=>($data['lastUpdateDate']=="0000-00-00"?" ":$data['lastUpdateDate']),   
        "column_tpbNo"=>$data['TPBNo'],  

        "column_function"=>"
            <div class='btn-group' role='group' aria-label=''>
                <div class='btn-group' role='group'>
                    <button id='btnGroupDrop".$data['tpbID']."' type='button' class='btn btn-outline-dark dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false'>
                        ".L('Actions')."
                    </button>
                <ul class='dropdown-menu' aria-labelledby='btnGroupDrop".$data['tpbID']."'>
                    <li>
                        <div class='d-grid'>
                            <button class='btn btn-md btn-outline-dark btnView' type='button' data-id='".$data['tpbID']."'><i class='fas fa-sm fa-eye'></i> ".L('View')."</button>
                            <button class='btn btn-md btn-outline-dark btnEdit' type='button' data-id='".$data['tpbID']."'><i class='fas fa-sm fa-edit'></i> ".L('Edit')."</button>
                            <button class='btn btn-md btn-outline-dark btnDel' type='button' data-id='".$data['tpbID']."'><i class='fas fa-sm fa-trash-alt'></i> ".L('Delete')."</button>                           
                        </div>
                    </li>
                </ul>
                </div>
            </div>
        "
    ]; 

    $lineCount++;
    $contentArr[] = $dataArr;
}

$returnArr['draw'] = intval($draw);
$returnArr["iTotalDisplayRecords"] = $stmAll->rowCount();
$returnArr["iTotalRecords"] = $stmAll->rowCount();
$returnArr["data"] = $contentArr;

echo json_encode($returnArr);

?>