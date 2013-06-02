<?php
namespace EcampCore;

use Zend\Stdlib\ArrayUtils;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\Feature\ServiceProviderInterface;

use EcampCore\EntityUtil\ServiceLocatorAwareEventListener;

class Module implements 
	ServiceProviderInterface
{
    public function getConfig(){
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig(){
    	
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function getServiceConfig(){
    	return include __DIR__ . '/config/service.config.php';
    }
    
    public function getControllerConfig(){
    	return include __DIR__ . '/config/controller.config.php';
    }
    
}
