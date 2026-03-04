<?php

use yii\db\Migration;

/**
 * Class m210112_063612_add_deafault_values_to_tables
 */
class m210112_063612_add_deafault_values_to_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //regionlarni qo'shish
        // xml file path
        $pathRegions = __DIR__ . "/regions.xml";
        // Read entire file into string
        $regionsXml = file_get_contents($pathRegions);
        // Convert xml string into an object
        $regionsNew = simplexml_load_string($regionsXml);
        // Convert into json
        $regionsCon = json_encode($regionsNew);
        // Convert into associative array
        $regions = json_decode($regionsCon, true)['table_regions']['regions'];
        foreach ($regions as $region){
            $this->insert('{{%regions}}',[
                'id' => $region['@attributes']['id'],
                'name' => $region['@attributes']['name_uz'],
                'key' => $region['@attributes']['key']
            ]);
        }
        //districtlarni qo'shish
        // xml file path
        $pathDistricts = __DIR__ . "/districts.xml";
        // Read entire file into string
        $ditrictsXml = file_get_contents($pathDistricts);
        // Convert xml string into an object
        $ditrictsNew = simplexml_load_string($ditrictsXml);
        // Convert into json
        $ditrictsCon = json_encode($ditrictsNew);
        // Convert into associative array
        $districts = json_decode($ditrictsCon, true)['table_districts']['districts'];
        foreach ($districts as $district){
            $this->insert('{{%districts}}',[
                'id' => $district['@attributes']['id'],
                'region_id' => $district['@attributes']['region_id'],
                'name' => $district['@attributes']['name_uz'],
                'key' => $district['@attributes']['key']
            ]);
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }
}
