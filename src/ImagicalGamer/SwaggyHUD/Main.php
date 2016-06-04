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
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
    $this->getServer()->getScheduler()->scheduleRepeatingTask(new SwaggyHUD($this), 1);
    $this->getLogger()->info(C::GREEN . "Enabled!");
  }
}
class SwaggyHUD extends PluginTask {
  
  public function __construct($plugin)
  {
    $this->plugin = $plugin;
    parent::__construct($plugin);
  }
  public function onRun($tick){
    $config = new Config($this->plugin->getDataFolder() . "/config.yml", Config::YAML);
    $allplayers = $this->plugin->getServer()->getOnlinePlayers();
    foreach($allplayers as $p) {
      if($p instanceof Player) {  
            //Start message
    if($config->get("Enable-Money") == true){
      $this->economy = $this->plugin->getServer()->getPluginManager()->getPlugin("EconomyAPI");
      $money = $this->economy->myMoney($p);
    }
    else{
      $money = null;
    }
    if($config->get("Enable-Stats") == true){
      $this->stats = $this->plugin->getServer()->getPluginManager()->getPlugin("PlayerStats");
      $kd = $this->stats->getStats($p);
      $kills = $this->stats->getKills($p);
      $deaths = $this->stats->getDeaths($p);
    }
    else{
      $kd = null;
      $deaths = null;
      $kills = null;
    }
    $message = $config->get("Message");
    $a = str_replace("&","§",$message);
    $ab = str_replace("{LINE}", "\n", $a);
    $abc = str_replace("{KILLS}", $kills, $ab);
    $abcd = str_replace("{DEATHS}",$deaths, $abc);
    $abcde = str_replace("{DEATHS}",$deaths, $abcd);
    $abcdef = str_replace("{KD}",$kd, $abcde);
    $msg = str_replace("{MONEY}",$money, $abcdef);

    //end message
        $p->sendPopup($msg . C::RESET . C::RESET);
      }
    }
  }
}
