<?php
namespace ImagicalGamer\SwaggyHUD;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\PluginTask;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\utils\TextFormat as C;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener{

  public function onEnable(){
    $this->saveDefaultConfig();
    $config = new Config($this->getDataFolder() . "/config.yml", Config::YAML);
    $format = $config->get("Format");
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
    $this->getLogger()->info(C::GREEN . "Enabled!");
    $this->getServer()->getScheduler()->scheduleRepeatingTask(new SwaggyHUD($this), 1);
    $this->getLogger()->notice(C::AQUA . "Message Format: " . $format);
  }
  public function onDeath(PlayerDeathEvent $event){
  	$data = new Config($this->getDataFolder() . "/data.yml", Config::YAML);
  	$entity = $event->getEntity();
  	if($entity instanceof Player){
        $name = $event->getEntity()->getName();
  	$deaths = $data->get($name);
  	$data->set($name,$deaths+1);
  	$data->save();
  	}
  }
  public function getMessage(){
    $config = new Config($this->getDataFolder() . "/config.yml", Config::YAML);
    $death = $this->plugin->getStats();
    $message = $config->get("Message");
    $m = str_replace("&","§",$message);
    $ms = str_replace("{LINE}","\n",$m);
    $msg = str_replace("{DEATH}",$death,$ms);
	return $msg;
  }
  public function getStats(Player $event){
  	$player = $event->getPlayer()->getName();
  	$data = new Config($this->getDataFolder() . "/data.yml", Config::YAML);
  	$death = $data->get($player);
  	return $death;
  }
  public function getFormat(){
  	$config = new Config($this->getDataFolder() . "/config.yml", Config::YAML);
	$format = $config->get("Format");
	return $format;
  }
  public function getSidebar(){
  	$config = new Config($this->getDataFolder() . "/config.yml", Config::YAML);
	$death = $this->plugin->getStats();
        $smessage = $config->get("Message");
        $s = str_replace("&","§",$message);
        $side = str_replace("{LINE}","\n                              ",$s);
        $sidebar = str_replace("{DEATH}",$death,$side);
	return $sidebar;
  }
}
class SwaggyHUD extends PluginTask {
  
	public function __construct($plugin)
	{
		$this->plugin = $plugin;
		parent::__construct($plugin);
	}
	public function onRun($tick){
		$allplayers = $this->plugin->getServer()->getOnlinePlayers();
		$message = $this->plugin->getMessage();
		$format = $this->plugin->getFormat();
		$sidebar = $this->plugin->getSidebar();
		foreach($allplayers as $p) {
			if($p instanceof Player) {	
                           if($format === "Popup"){
				$p->sendPopup($message);
			        }
				if($format === "Tip"){
				$p->sendTip($message);
				}
				if($format === "Sidebar"){
				$p->sendTip("                              " . $sidebar);
				}
			}
		}
	}
}
