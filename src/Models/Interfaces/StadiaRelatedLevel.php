<?php


namespace JohanKladder\Stadia\Models\Interfaces;


interface StadiaRelatedLevel
{
    public function getId();

    public function getTableName();

    public function getFormattedName();

    public function getImageUrl();

    public function getPlantReferenceId();

    public function getDurationInDays();

}
