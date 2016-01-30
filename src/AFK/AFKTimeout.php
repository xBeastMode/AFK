<?php
namespace AFK;
use pocketmine\scheduler\PluginTask;
use pocketmine\level\Level;

class AFKTimeout extends PluginTask {

    public function __construct(Main $plugin) {
        parent::__construct($plugin);
        $this->plugin = $plugin;
    }

    public function onRun($currentTick) {

        foreach($this->plugin->getServer()->getOnlinePlayers() as $p) {
            if(!isset($this->plugin->time[$p->getName()])) {
                $this->plugin->setTime($p);
            }

            if(!isset($this->plugin->pos[$p->getName()])) {
                $this->plugin->setPos($p);
            }

            if(!$this->plugin->hasMoved($p)) {
                $this->plugin->removePlayer($p);
            }elseif(!$this->plugin->isPlayerSet($p)) {
                $this->plugin->setPlayer($p);
            }
        }
        $this->plugin->checkTime();
    }
}
?>