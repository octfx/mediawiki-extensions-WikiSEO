<?php

declare(strict_types=1);

namespace phpunit;

use IDatabase;
use MediaWiki\Extension\WikiSEO\OverwritePageImageProp;
use MediaWiki\Title\Title;
use MediaWikiIntegrationTestCase;
use Wikimedia\Rdbms\LoadBalancer;

class OverwritePageImagePropTest extends MediaWikiIntegrationTestCase
{
    /**
     * @covers \MediaWiki\Extension\WikiSEO\OverwritePageImageProp
     * @return void
     */
    public function testConstructor() {
        $title = Title::makeTitle(NS_MAIN, 'Foo');
        $prop = new OverwritePageImageProp($title, '');

        $this->assertInstanceOf(OverwritePageImageProp::class, $prop);
    }

    /**
     * @covers \MediaWiki\Extension\WikiSEO\OverwritePageImageProp::doUpdate
     * @return void
     */
    public function testDoUpdateEmpty() {
        $title = Title::makeTitle(NS_MAIN, 'Foo');
        $prop = new OverwritePageImageProp($title, '');

        $dbLBMock = $this->getMockBuilder(LoadBalancer::class)->disableOriginalConstructor()->getMock();
        $dbLBMock->expects($this->never())->method('getConnection');

        $this->setService('DBLoadBalancer', $dbLBMock);

        $prop->doUpdate();
    }

    /**
     * @covers \MediaWiki\Extension\WikiSEO\OverwritePageImageProp::doUpdate
     * @return void
     */
    public function testDoUpdate() {
        $title = Title::makeTitle(NS_MAIN, 'Foo');
        $prop = new OverwritePageImageProp($title, 'Foo.jpg');

        $dbMock = $this->getMockBuilder(IDatabase::class)->getMock();
        $dbMock->expects($this->once())->method('update')->with(
            'page_props',
            [ 'pp_value' => 'Foo.jpg' ],
            ['pp_page' => $title->getId(), 'pp_propname' => 'page_image_free']
        );

        $dbLBMock = $this->getMockBuilder(LoadBalancer::class)->disableOriginalConstructor()->getMock();
        $dbLBMock->expects($this->once())->method('getConnection')->willReturn($dbMock);

        $this->setService('DBLoadBalancer', $dbLBMock);

        $prop->doUpdate();
    }

    /**
     * @covers \MediaWiki\Extension\WikiSEO\OverwritePageImageProp
     * @covers \MediaWiki\Extension\WikiSEO\OverwritePageImageProp::doUpdate
     * @return void
     */
    public function testDoUpdateFullTitle() {
        $title = Title::makeTitle(NS_MAIN, 'Foo');

        $prop = new OverwritePageImageProp($title, 'File:Foo.jpg');

        $dbMock = $this->getMockBuilder(IDatabase::class)->getMock();
        $dbMock->expects($this->once())->method('update')->with(
            'page_props',
            [ 'pp_value' => 'Foo.jpg' ],
            ['pp_page' => $title->getId(), 'pp_propname' => 'page_image_free']
        );

        $dbLBMock = $this->getMockBuilder(LoadBalancer::class)->disableOriginalConstructor()->getMock();
        $dbLBMock->expects($this->once())->method('getConnection')->willReturn($dbMock);

        $this->setService('DBLoadBalancer', $dbLBMock);

        $prop->doUpdate();
    }
}