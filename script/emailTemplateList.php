<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
include_once('../inc/global.php');
include_once("../config/route.php");
$user = unserialize($_SESSION['user']);
$today = Utility\WebSystem::displayDate(date("Y-m-d H:i:s"), 'Y-m-d');


$map = [
    'column_emailTemplateID' => 'emailTemplate.id',
    'column_emailTemplateName' => 'emailTemplate.name',
    'column_emailTemplateSubject' => 'emailTemplate.subject',
    'column_emailTemplateStatus' => 'status.name',
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

$sqlAll = Database\Sql::select(['emailTemplate', 'emailTemplate'])->leftJoin(['status', 'status'], "emailTemplate.status = status.id");
            
$sqlAll->setFieldValue('
    emailTemplate.id emailTemplateID, 
    emailTemplate.name emailTemplateName, 
    emailTemplate.subject emailTemplateSubject, 
    status.name statusName
');

if($searchValue != ''){
    $searchValue = addslashes($searchValue);
    $sqlAll->whereOp("(emailTemplate.id LIKE '%".$searchValue."%' 
        OR emailTemplate.name LIKE '%".$searchValue."%'
        OR emailTemplate.subject LIKE '%".$searchValue."%' 
    )");
}


$filter = false;
foreach($_POST['columns'] as $idx => $column){
    if(!empty($column['search']['value'])) {
        $filter = true;
        $idxSearchValue = substr($column['search']['value'], 1, -1);
        if($_POST['columns'][$idx]['data']=="column_emailTemplateStatus"){
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

//$sqlAll->where(['1', 'GROUP BY', 'tpb.id']);

$stmAll = $sqlAll->prepare();
$stmAll->execute();

if($stmAll->rowCount()==0 && !$filter) {

    $sqlAll = Database\Sql::select(['emailTemplate', 'emailTemplate'])->leftJoin(['status', 'status'], "emailTemplate.status = status.id");
            
    $sqlAll->setFieldValue('
        emailTemplate.id emailTemplateID, 
        emailTemplate.name emailTemplateName, 
        emailTemplate.subject emailTemplateSubject, 
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
        "column_emailTemplateID"=>$data['emailTemplateID'],
        "column_emailTemplateName"=>$data['emailTemplateName'], 
        "column_emailTemplateSubject"=>$data['emailTemplateSubject'], 
        "column_emailTemplateStatus"=>L($data['statusName']),  
        "column_function"=>"
            <div class='btn-group' role='group' aria-label=''>
                <div class='btn-group' role='group'>
                    <button id='btnGroupDrop".$data['emailTemplateID']."' type='button' class='btn btn-outline-dark dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false'>
                        ".L('Actions')."
                    </button>
                <ul class='dropdown-menu' aria-labelledby='btnGroupDrop".$data['emailTemplateID']."'>
                    <li>
                        <div class='d-grid'>
                            <button class='btn btn-md btn-outline-dark btnView' type='button' data-id='".$data['emailTemplateID']."'><i class='fas fa-sm fa-eye'></i> ".L('View')."</button>                                
                            <button class='btn btn-md btn-outline-dark btnEdit' type='button' data-id='".$data['emailTemplateID']."'><i class='fas fa-sm fa-edit'></i> ".L('Edit')."</button>
                            <button class='btn btn-md btn-outline-dark btnDel' type='button' data-id='".$data['emailTemplateID']."'><i class='fas fa-sm fa-trash-alt'></i> ".L('Delete')."</button>                           
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