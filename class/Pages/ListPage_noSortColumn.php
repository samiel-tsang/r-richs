<?php
namespace Pages;

use Requests\Request;
use Database\Sql, Database\Listable;
use Routing\Route;
use Utility\WebSystem;

class ListPage extends Page {
	
	private $sql;
	protected $pageNo;
	protected $listMax;
	protected $pageTotal;
	
	protected $lister;
	
	public function __construct(String $view, Sql $sqlObj, Array $vals = []) {
		parent::__construct($view, $vals);
		$this->updateSql($sqlObj);
	}
	
	public function updateSql(Sql $sqlObj) {
		$this->sql = $sqlObj;
		
		$count = $this->ListCount();
		
		$this->pageNo = 1;
		$this->listMax = cfg()['listMax'] ?? 30;
		$this->pageTotal = ceil($count / $this->listMax);
		
		$request = Request::get();
		if ($request->isValued('pg') ) {
			$pg = $request->existsIn('pg');
			if (is_numeric($pg) && $pg <= $this->pageTotal) {
				$this->pageNo = intval(trim($pg));
			}
		}
		
		$this->setValues(['itemCount'=>$count, 'pg'=>$this->pageNo, 'pageTotal'=>$this->pageTotal]);
	}
	
	public function ListCount() {
		$sql = clone $this->sql;
		$stm = db()->prepare($sql);
		$stm->execute();
		
		return $stm->rowCount();
	}
	
	public function setLister(Listable $lister = null) { $this->lister = $lister; }
	
	public function ListItem() {
		$sql = clone $this->sql;
		$sql->limit($this->listMax, ($this->pageNo - 1) * $this->listMax);
		$stm = db()->prepare($sql);
		$stm->setFetchMode(\PDO::FETCH_NAMED);
		$stm->execute();
		foreach ($stm as $row) {
			if (is_null($this->lister)) {
				yield ((object) $row);
			} else {
				yield $this->lister->extraProcess((object) $row);
			}
		}
	}
	
	public function pagination(Array $value = []) {
		$havePrev = ($this->pageTotal > 1 && $this->pageNo != 1);
		$haveNext = ($this->pageTotal > 1 && $this->pageNo < $this->pageTotal);
		$isFirst = ($this->pageNo == 1);
		$isLast = ($this->pageNo == $this->pageTotal);
		
		$start = (($this->pageNo - 5) > 0)?($this->pageNo - 5):1;
		$end = (($this->pageNo + 5) > $this->pageTotal)?$this->pageTotal:($this->pageNo + 5);
		
		include("view/layout/pagination.php");
	}
	
	public function pageNameLink(Route $path, int $pg) {
		$request = Request::get();
		$querystr = clone $request->get;
		$querystr->pg = $pg;
		
		return WebSystem::path($path->path($querystr->toArray()), false, false);
	}
}