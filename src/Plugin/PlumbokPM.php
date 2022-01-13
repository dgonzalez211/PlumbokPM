<?php

namespace Octopush\Plumbok\Plugin;

use Phar;
use pocketmine\plugin\PluginBase;

class PlumbokPM extends PluginBase
{
	public function onLoad(): void {
		if (($phar = Phar::running(true)) !== "") {
			if (!defined("Octopush\PlumbokPM\COMPOSER")) {
				define("Octopush\PlumbokPM\DATA_PATH", $this->getDataFolder());
				define("Octopush\PlumbokPM\VERSION", "v" . $this->getDescription()->getVersion());
				define("Octopush\PlumbokPM\COMPOSER", $phar . "/vendor/autoload.php");
			}
		}

		$this->saveDefaultConfig();
	}

	public function onEnable(): void {
		if (extension_loaded("xdebug")) {
			if (ini_get("xdebug.output_dir") === $this->getDataFolder()) {
				$this->getLogger()->warning("X-Debug is running, this will cause data pack to be several minutes long.");
			} else {
				$this->getLogger()->emergency("Plugin will not run with xdebug due to the performance drops.");
				$this->getServer()->getPluginManager()->disablePlugin($this);
			}
		}
	}
}