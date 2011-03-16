<?php
/*
 * Copyright (C) 2011 Pirmin Mattmann
 *
 * This file is part of eCamp.
 *
 * eCamp is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * eCamp is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with eCamp.  If not, see <http://www.gnu.org/licenses/>.
 */


class GroupController extends \Controller\BaseController
{

	/**
	 * @var Zend_Session_Namespace
	 */
	private $authSession;

    public function init()
    {
	    parent::init();
    }

    public function showAction()
    {
		$id = $this->getRequest()->getParam("group");
		$group = $this->em->getRepository("Entity\Group")->find($id);
		
		$this->view->group = $group;		
    }
	
	public function avatarAction()
	{
		$id = $this->getRequest()->getParam("group");
		
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$group = $this->em->getRepository("Entity\Group")->find($id);
		
		if( $group->getImageData() == null ) {
			$this->_redirect("img/default_group.png");
			
		} else {
			$this->getResponse()->setHeader("Content-type", $group->getImageMime());
			$this->getResponse()->setBody($group->getImageData());
		}
	}
	
	

}