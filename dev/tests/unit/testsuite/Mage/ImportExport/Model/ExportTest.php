<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  unit_tests
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test class for Mage_ImportExport_Model_Export
 */
class Mage_ImportExport_Model_ExportTest extends PHPUnit_Framework_TestCase
{
    /**
     * Extension for export file
     *
     * @var string
     */
    protected $_exportFileExtension = 'csv';

    /**
     * Return mock for Mage_ImportExport_Model_Export class
     *
     * @return Mage_ImportExport_Model_Export
     */
    protected function _getMageImportExportModelExportMock()
    {
        /** @var $mockEntityAbstract Mage_ImportExport_Model_Export_EntityAbstract */
        $mockEntityAbstract = $this->getMockForAbstractClass(
            'Mage_ImportExport_Model_Export_EntityAbstract',
            array(),
            '',
            false
        );

        /** @var $mockAdapterTest Mage_ImportExport_Model_Export_Adapter_Abstract */
        $mockAdapterTest = $this->getMockForAbstractClass(
            'Mage_ImportExport_Model_Export_Adapter_Abstract',
            array(),
            '',
            false,
            true,
            true,
            array('getFileExtension')
        );
        $mockAdapterTest->expects($this->any())
            ->method('getFileExtension')
            ->will($this->returnValue($this->_exportFileExtension));

        /** @var $mockModelExport Mage_ImportExport_Model_Export */
        $mockModelExport = $this->getMock(
            'Mage_ImportExport_Model_Export',
            array('getEntityAdapter', '_getEntityAdapter', '_getWriter')
        );
        $mockModelExport->expects($this->any())
            ->method('getEntityAdapter')
            ->will($this->returnValue($mockEntityAbstract));
        $mockModelExport->expects($this->any())
            ->method('_getEntityAdapter')
            ->will($this->returnValue($mockEntityAbstract));
        $mockModelExport->expects($this->any())
            ->method('_getWriter')
            ->will($this->returnValue($mockAdapterTest));

        return $mockModelExport;
    }

    /**
     * Test get file name with adapter file name
     */
    public function testGetFileNameWithAdapterFileName()
    {
        $model = $this->_getMageImportExportModelExportMock();
        $basicFileName = 'test_file_name';
        $model->getEntityAdapter()->setFileName($basicFileName);

        $fileName = $model->getFileName();
        $correctDateTime = $this->_getCorrectDateTime($fileName);
        $this->assertNotNull($correctDateTime);

        $correctFileName = $basicFileName . '_' . $correctDateTime . '.' . $this->_exportFileExtension;
        $this->assertEquals($correctFileName, $fileName);
    }

    /**
     * Test get file name without adapter file name
     */
    public function testGetFileNameWithoutAdapterFileName()
    {
        $model = $this->_getMageImportExportModelExportMock();
        $model->getEntityAdapter()->setFileName(null);
        $basicFileName = 'test_entity';
        $model->setEntity($basicFileName);

        $fileName = $model->getFileName();
        $correctDateTime = $this->_getCorrectDateTime($fileName);
        $this->assertNotNull($correctDateTime);

        $correctFileName = $basicFileName . '_' . $correctDateTime . '.' . $this->_exportFileExtension;
        $this->assertEquals($correctFileName, $fileName);
    }

    /**
     * Get correct file creation time
     *
     * @param string $fileName
     * @return string|null
     */
    protected function _getCorrectDateTime($fileName)
    {
        preg_match('/(\d{8}_\d{6})/', $fileName, $matches);
        if (isset($matches[1])) {
            return $matches[1];
        }
        return null;
    }
}
