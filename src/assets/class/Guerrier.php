<?php 
require_once'Character.php';

class Guerrier extends Character
{
    private string $weapon;
    private int $weaponDamage;
    private string $shield;
    private int $shieldValue;


    //CONSTRUCT
    function __construct(int $newHealth, int $newMana, string $newWeapon, int $weaponDamage, string $newShield, int $shieldAbsorbtion) { //Au tout début, sert à définir un nouvelle objet
        parent::__construct($newHealth, $newMana);
        $this->setWeapon($newWeapon);
        $this->setWeaponDamage($weaponDamage);
        $this->setShield($newShield);
        $this->setShieldAbsorbtion($shieldAbsorbtion);
    }


    // SETTER
    public function setWeapon(string $newWeapon) { //Setter pour modifier la propriété privée
        $this->weapon = $newWeapon;
    }

    public function setWeaponDamage(int $weaponDamage) { //Setter pour modifier la propriété privée
        $this->weaponDamage = $weaponDamage;
    }

    public function setShield(string $newShield) { //Setter pour modifier la propriété privée
        $this->shield = $newShield;
    }

    public function setShieldAbsorbtion(int $shieldAbsorbtion) { //Setter pour modifier la propriété privée
        $this->shieldValue = $shieldAbsorbtion;
    }


    // GETTER
    public function getWeapon() { //Setter pour modifier la propriété privée
        return $this->weapon;
    }

    public function getWeaponDamage() { //Setter pour modifier la propriété privée
        return $this->weaponDamage;
    }

    public function getShield() { //Setter pour modifier la propriété privée
        return $this->shield;
    }

    public function getShieldAbsorbtion() { //Setter pour modifier la propriété privée
        return $this->shieldValue;
    }


    // METHODE DE COMBAT
    public function attack() {
        return $this->getWeaponDamage();
    }

    public function getDamage(int $damage) {
        if (($damage - $this->getShieldAbsorbtion()) <= 0 ) {
            $this->setHealth($this->getHealth());
            return 0;
        } else {
            $this->setHealth(($this->getHealth()) - ($damage - $this->getShieldAbsorbtion()));
            return $damage - $this->getShieldAbsorbtion();
        }
    }

    public function magicUse() {
        $soin = rand(100, 500);
        return $soin;
    }
}

?>