<?php


namespace JohanKladder\Stadia\Models\Interfaces;


interface StadiaRelatedPlant
{
    public function getId();

    public function getTableName();

    public function getFormattedName();

    public function getImageUrl();

}
