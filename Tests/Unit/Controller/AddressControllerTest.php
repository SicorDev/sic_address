<?php
namespace SICOR\SicAddress\Tests\Unit\Controller;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 SICOR DEVTEAM <dev@sicor-kdl.net>, Sicor KDL GmbH
 *  			
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Test case for class SICOR\SicAddress\Controller\AddressController.
 *
 * @author SICOR DEVTEAM <dev@sicor-kdl.net>
 */
class AddressControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{

	/**
	 * @var \SICOR\SicAddress\Controller\AddressController
	 */
	protected $subject = NULL;

	public function setUp()
	{
		$this->subject = $this->getMock('SICOR\\SicAddress\\Controller\\AddressController', array('redirect', 'forward', 'addFlashMessage'), array(), '', FALSE);
	}

	public function tearDown()
	{
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function listActionFetchesAllAddressesFromRepositoryAndAssignsThemToView()
	{

		$allAddresses = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array(), array(), '', FALSE);

		$addressRepository = $this->getMock('SICOR\\SicAddress\\Domain\\Repository\\AddressRepository', array('findAll'), array(), '', FALSE);
		$addressRepository->expects($this->once())->method('findAll')->will($this->returnValue($allAddresses));
		$this->inject($this->subject, 'addressRepository', $addressRepository);

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$view->expects($this->once())->method('assign')->with('addresses', $allAddresses);
		$this->inject($this->subject, 'view', $view);

		$this->subject->listAction();
	}

	/**
	 * @test
	 */
	public function showActionAssignsTheGivenAddressToView()
	{
		$address = new \SICOR\SicAddress\Domain\Model\Address();

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$this->inject($this->subject, 'view', $view);
		$view->expects($this->once())->method('assign')->with('address', $address);

		$this->subject->showAction($address);
	}

	/**
	 * @test
	 */
	public function createActionAddsTheGivenAddressToAddressRepository()
	{
		$address = new \SICOR\SicAddress\Domain\Model\Address();

		$addressRepository = $this->getMock('SICOR\\SicAddress\\Domain\\Repository\\AddressRepository', array('add'), array(), '', FALSE);
		$addressRepository->expects($this->once())->method('add')->with($address);
		$this->inject($this->subject, 'addressRepository', $addressRepository);

		$this->subject->createAction($address);
	}

	/**
	 * @test
	 */
	public function editActionAssignsTheGivenAddressToView()
	{
		$address = new \SICOR\SicAddress\Domain\Model\Address();

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$this->inject($this->subject, 'view', $view);
		$view->expects($this->once())->method('assign')->with('address', $address);

		$this->subject->editAction($address);
	}

	/**
	 * @test
	 */
	public function updateActionUpdatesTheGivenAddressInAddressRepository()
	{
		$address = new \SICOR\SicAddress\Domain\Model\Address();

		$addressRepository = $this->getMock('SICOR\\SicAddress\\Domain\\Repository\\AddressRepository', array('update'), array(), '', FALSE);
		$addressRepository->expects($this->once())->method('update')->with($address);
		$this->inject($this->subject, 'addressRepository', $addressRepository);

		$this->subject->updateAction($address);
	}

	/**
	 * @test
	 */
	public function deleteActionRemovesTheGivenAddressFromAddressRepository()
	{
		$address = new \SICOR\SicAddress\Domain\Model\Address();

		$addressRepository = $this->getMock('SICOR\\SicAddress\\Domain\\Repository\\AddressRepository', array('remove'), array(), '', FALSE);
		$addressRepository->expects($this->once())->method('remove')->with($address);
		$this->inject($this->subject, 'addressRepository', $addressRepository);

		$this->subject->deleteAction($address);
	}
}
