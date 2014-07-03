<?php

class Makeweb_Elements_RightsHelper
{
    protected static $_rights = null;

    public static function getAllRights()
    {
        if (self::$_rights === null) {
            $container = MWF_Registry::getContainer();
            $rights = $container->componentCallback->getAccessRights();

            foreach ($rights as $component => $componentRights) {
                foreach ($componentRights as $componentRight => $val) {
                    self::$_rights[] = $componentRight;
                }
            }
        }

        return self::$_rights;
    }
}