<?php
include_once('../inc/global.php');
include_once("../config/route.php");
$user = unserialize($_SESSION['user']);
$today = Utility\WebSystem::displayDate(date("Y-m-d H:i:s"), 'Y-m-d');


$map = [
    'column_submissionModeStatus' => 'status.name',
    'column_submissionModeID' => 'submissionMode.id',
    'column_submissionModeName' => 'submissionMode.name',
    'column_function' => 'clientType.id'
];

$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = $_POST['search']['value']; // Search value
$searchQuery = " ";

$sqlAll = Database\Sql::select(['submissionMode', 'submissionMode'])->leftJoin(['status', 'status'], "submissionMode.status = status.id");

$sqlAll->where(['submissionMode.status', '=', 1]);

$sqlAll->setFieldValue('
    submissionMode.id submissionModeID, 
    submissionMode.name submissionModeName,
    status.name statusName    
');

if($searchValue != ''){
    $searchValue = addslashes($searchValue);
    $sqlAll->whereOp("(submissionMode.id LIKE '%".$searchValue."%' 
        OR submissionMode.name LIKE '%".$searchValue."%'
        OR status.name LIKE '%".$searchValue."%'
    )");
}

foreach($_POST['columns'] as $idx => $column){
    if(!empty($column['search']['value'])) {
        $idxSearchValue = substr($column['search']['value'], 1, -1);
        if($_POST['columns'][$idx]['data']=="column_submissionModeStatus"){
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
    $sqlAll = Database\Sql::select(['submissionMode', 'submissionMode'])->leftJoin(['status', 'status'], "submissionMode.status = status.id");

    $sqlAll->where(['submissionMode.status', '=', 1]);
    
    $sqlAll->setFieldValue('
        submissionMode.id submissionModeID, 
        submissionMode.name submissionModeName,
        status.name statusName    
    ');
};

$sql = $sqlAll->order($map[$columnName],$columnSortOrder)->limit($rowperpage, $row);

$stm = $sql->prepare();
$stm->execute();

$returnArr = [];
$contentArr = [];
$lineCount = 0;
foreach($stm as $data){    

    $dataArr = [
        "column_submissionModeStatus"=>L($data['statusName']),
        "column_submissionModeID"=>$data['submissionModeID'],
        "column_submissionModeName"=>$data['submissionModeName'],     
        "column_function"=>"
            <div class='btn-group' role='group' aria-label=''>
                <div class='btn-group' role='group'>
                    <button id='btnGroupDrop".$data['submissionModeID']."' type='button' class='btn btn-outline-dark dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false'>
                        ".L('Actions')."
                    </button>
                <ul class='dropdown-menu' aria-labelledby='btnGroupDrop".$data['submissionModeID']."'>
                    <li>
                        <div class='d-grid'>
                            <button class='btn btn-md btn-outline-dark btnView' type='button' data-id='".$data['submissionModeID']."'><i class='fas fa-sm fa-eye'></i> ".L('View')."</button>
                            <button class='btn btn-md btn-outline-dark btnEdit' type='button' data-id='".$data['submissionModeID']."'><i class='fas fa-sm fa-edit'></i> ".L('Edit')."</button>
                            <button class='btn btn-md btn-outline-dark btnDel' type='button' data-id='".$data['submissionModeID']."'><i class='fas fa-sm fa-trash-alt'></i> ".L('Delete')."</button>
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