<?php

namespace app\modules\yiipass\tests;

use Yii;
use yii\codeception\TestCase;
use yii\web\Application;


/**
 * Class KeePassXValidXmlExportTest
 *
 * This class provides a test for the XML creation algorithm, which creates
 * an array that can be imported into KeePass password manager database.
 * During the "Mocking" test method, the algorithm test is independent from
 * any database. All test data is provided inside the dataProvider method
 * getKeePassExportTestData().
 *
 * @package app\modules\yiipass\tests
 */
class KeePassXValidXmlExportTest extends TestCase {

    public $appConfig = '@tests/unit/_web.php';


    protected function setUp()
    {
        // don't forget to call parent method that will setup Yii application
        parent::setUp();
        $debug = true;
    }

        /**
     * This method is a dataprovider for the testExport(); method. The
     * dataprovider is used for the "Mocking" testing method.
     *
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getKeePassExportTestData(){
        /**
         * The data provider is run by PHPUnit before the setUp()
         * is called. So Yii is booted in the data provider method.
         */
        require_once(__DIR__ . '/_bootstrap.php');

        // Gets $arr_for_xml to create xml for comparison.
        include_once(__DIR__ . '/fixtures/XmlExportArr.php');

        // Gets $expected_xml to compare with $new_xml
        include_once(__DIR__ . '/fixtures/XmlExport.php');

        //$kpx = Yii::$app->getModule('yiipass')->get('SimpleKeePassXmlService');
        //$new_xml = $kpx->createKeePassValidXml($arr_for_xml);

        $new_xml = '';

        (new Application($this->appConfig))->run();

        // The array for return. Every key is a parameter.
        $out[] = [
            'newly_created_xml' => $new_xml,
            'proven_xml' => $expected_xml
        ];

        return $out;
    }

    /**
     * This method is testing the algorithm for the XML file creation by
     * generating an new XML file from an serialized array and comparing
     * it with a previously generated XML file. The files should be the
     * same. After they're recognized as the same, chances are high, that
     * the algorithm still works after any modification.
     *
     * @dataProvider getKeePassExportTestData()
     */
    public function testExport($newly_created_xml, $expected_xml){
        $this->assertEquals($newly_created_xml, $expected_xml);
    }
}
