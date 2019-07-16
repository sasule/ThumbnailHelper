<?php

namespace Kollarovic\Thumbnail\DI;

use Nette;
use Tracy\Helpers;


if (!class_exists('Nette\DI\CompilerExtension')) {
	class_alias('Nette\Config\CompilerExtension', 'Nette\DI\CompilerExtension');
}


/**
 * Extension
 *
 * @author  Mario Kollarovic
 */
class Extension extends Nette\DI\CompilerExtension
{

	public $defaults = array(
		'wwwDir' => '%wwwDir%',
		'httpRequest' => '@httpRequest',
		'thumbPathMask' => 'images/thumbs/{filename}-{width}x{height}.{extension}',
		'placeholder' => 'http://dummyimage.com/{width}x{height}/efefef/f00&text=Image+not+found',
		'placeholderForCustomText' => 'http://dummyimage.com/{width}x{height}/efefef/f00&text={text}',
		'filterName' => 'thumbnail',
		'preferImagick' => false,
	);


	public function loadConfiguration()
	{
		$config = $this->getConfig();

		$config = Nette\Schema\Helpers::merge($this->defaults, $config);

		$config = Nette\DI\Helpers::expand($config, $this->getContainerBuilder()->parameters);

		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('thumbnail'))
			->setFactory('Kollarovic\Thumbnail\Generator', array(
				'wwwDir' => $config['wwwDir'],
				'httpRequest' => $config['httpRequest'],
				'thumbPathMask' => $config['thumbPathMask'],
				'placeholder' => $config['placeholder'],
				'placeholderForCustomText' => $config['placeholderForCustomText'],
				'preferImagick' => $config['preferImagick'],
			));

		if ($builder->hasDefinition('nette.latteFactory')) {
			$definition = $builder->getDefinition('nette.latteFactory');
			$definition->getResultDefinition()->addSetup('addFilter', array($config['filterName'], array($this->prefix('@thumbnail'), 'thumbnail')));
		}
	}

}
