<?php
namespace ExternalModules\FrameworkVersion2;

class User
{
	function __construct($framework, $username){
		$this->framework = $framework;
		$this->username = db_real_escape_string($username);
	}

	function getRights($project_ids = null){
		if($project_ids === null){
			$project_ids = $this->framework->requireProjectId();
		}

		if(!is_array($project_ids)){
			$project_ids = [$project_ids];
		}

		$rightsByPid = [];
		foreach($project_ids as $project_id){
			$rights = \UserRights::getPrivileges($project_id, $this->username);
			$rightsByPid[$project_id] = $rights[$project_id][$this->username];
		}

		return $rightsByPid;
	}

	function hasDesignRights(){
		if($this->isSuperUser()){
			return true;
		}

		$rights = $this->getRights();
		return $rights['design'] === 1;
	}

	private function getUserInfo(){
		if(!$this->user_info){
			$results = $this->framework->query("
				select *
				from redcap_user_information
				where username = '{$this->username}'
			");

			$this->user_info = $results->fetch_assoc();
		}

		return $this->user_info;
	}

	function isSuperUser(){
		$userInfo = $this->getUserInfo();
		return $userInfo['super_user'] === '1';
	}

	function getEmail(){
		$userInfo = $this->getUserInfo();
		return $userInfo['user_email'];
	}
}
