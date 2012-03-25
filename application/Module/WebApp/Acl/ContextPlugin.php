<?php

namespace WebApp\Acl;

use Core\Context;

class ContextPlugin extends \Zend_Controller_Plugin_Abstract
{
	
	/**
	 * @var CoreApi\Acl\ContextManager
	 * @Inject CoreApi\Acl\ContextManager
	 */
	private $contextManager;
	
	
	public function preDispatch(\Zend_Controller_Request_Abstract $request)
	{
		$userId  = is_int($request->getParam('user'))  ? $request->getParam('user') 	: null;
		$groupId = is_int($request->getParam('group')) ? $request->getParam('group') 	: null;
		$campId  = is_int($request->getParam('camp'))  ? $request->getParam('camp') 	: null;
		
		$this->contextManager->set($userId, $groupId, $campId);
	}
	
}