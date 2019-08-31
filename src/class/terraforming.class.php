<?php

use Stu\Orm\Entity\TerraformingCostInterface;
use Stu\Orm\Repository\TerraformingCostRepositoryInterface;

class TerraformingData extends BaseTable {

	protected $tablename = 'stu_terraforming';
	const tablename = 'stu_terraforming';
	
	function __construct(&$data = array()) {
		$this->data = $data;
	}

	function setId($value) {
		$this->data['id'] = $value;
	}

	function getId() {
		return $this->data['id'];
	}

	function getDescription() {
		return $this->data['description'];
	}

	function setDescription($value) {
		$this->data['description'] = $value;
	}

	function getEpsCost() {
		return $this->data['ecost'];
	}

	function setEpsCost($value) {
		$this->data['ecost'] = $value;
	}

	function getSource() {
		return $this->data['v_feld'];
	}

	function getDestination() {
		return $this->data['z_feld'];
	}

	function getResearchId() {
		return $this->data['research_id'];
	}

	function getLimit() {
		return $this->data['limit'];
	}

	function getDuration() {
		return $this->data['duration'];
	}

	/**
	 * @return TerraformingCostInterface[]
	 */
	function getCosts() {
		// @todo refactor
		global $container;

		return $container->get(TerraformingCostRepositoryInterface::class)->getByTerraforming((int) $this->getId());
	}


}
class Terraforming extends TerraformingData {

	function __construct(&$id=0) {
		$result = DB()->query("SELECT * FROM ".$this->getTable()." WHERE id=".$id." LIMIT 1",4);
		if ($result == 0) {
			new ObjectNotFoundException($id);
		}
		return parent::__construct($result);
	}

	static function getByDestination($destination=0, $userId) {
		$ret = array();
		$result = DB()->query("SELECT * FROM ".self::tablename." WHERE v_feld=".intval($destination)." AND (research_id=0 OR research_id IN (SELECT research_id FROM stu_researched WHERE user_id=".$userId." AND finished>0))");
		while ($data = mysqli_fetch_assoc($result)) {
			$ret[] = new TerraformingData($data);
		}
		return $ret;
	}

}
?>
