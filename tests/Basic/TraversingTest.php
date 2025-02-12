<?php

namespace Behat\Mink\Tests\Driver\Basic;

use Behat\Mink\Tests\Driver\TestCase;
use Yoast\PHPUnitPolyfills\Polyfills\AssertionRenames;

class TraversingTest extends TestCase
{
    use AssertionRenames;

    /**
     * find by label.
     *
     * @group issue211
     */
    public function testIssue211()
    {
        $this->getSession()->visit($this->pathTo('/issue211.html'));
        $field = $this->getSession()->getPage()->findField('Téléphone');

        $this->assertNotNull($field);
    }

    public function testElementsTraversing()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $page = $this->getSession()->getPage();

        $title = $page->find('css', 'h1');
        $this->assertNotNull($title);
        $this->assertEquals('Extremely useless page', $title->getText());
        $this->assertEquals('h1', $title->getTagName());

        $strong = $page->find('xpath', '//div/strong[3]');
        $this->assertNotNull($strong);
        $this->assertEquals('pariatur', $strong->getText());
        $this->assertEquals('super-duper', $strong->getAttribute('class'));
        $this->assertTrue($strong->hasAttribute('class'));

        $strong2 = $page->find('xpath', '//div/strong[2]');
        $this->assertNotNull($strong2);
        $this->assertEquals('veniam', $strong2->getText());
        $this->assertEquals('strong', $strong2->getTagName());
        $this->assertNull($strong2->getAttribute('class'));
        $this->assertFalse($strong2->hasAttribute('class'));

        $strongs = $page->findAll('css', 'div#core > strong');
        $this->assertCount(3, $strongs);
        $this->assertEquals('Lorem', $strongs[0]->getText());
        $this->assertEquals('pariatur', $strongs[2]->getText());

        $element = $page->find('css', '#some-element');

        $this->assertNotNull($element);
        $this->assertEquals('some very interesting text', $element->getText());
        $this->assertEquals(
            "\n            some <div>very\n            </div>\n".
            "<em>interesting</em>      text\n        ",
            $element->getHtml()
        );

        $this->assertTrue($element->hasAttribute('data-href'));
        $this->assertFalse($element->hasAttribute('data-url'));
        $this->assertEquals('http://mink.behat.org', $element->getAttribute('data-href'));
        $this->assertNull($element->getAttribute('data-url'));
        $this->assertEquals('div', $element->getTagName());
    }

    public function testVeryDeepElementsTraversing()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $page = $this->getSession()->getPage();

        $footer = $page->find('css', 'footer');
        $this->assertNotNull($footer);

        $searchForm = $footer->find('css', 'form#search-form');
        $this->assertNotNull($searchForm);
        $this->assertEquals('search-form', $searchForm->getAttribute('id'));

        $searchInput = $searchForm->findField('Search site...');
        $this->assertNotNull($searchInput);
        $this->assertEquals('text', $searchInput->getAttribute('type'));

        $searchInput = $searchForm->findField('Search site...');
        $this->assertNotNull($searchInput);
        $this->assertEquals('text', $searchInput->getAttribute('type'));

        $profileForm = $footer->find('css', '#profile');
        $this->assertNotNull($profileForm);

        $profileFormDiv = $profileForm->find('css', 'div');
        $this->assertNotNull($profileFormDiv);

        $profileFormDivLabel = $profileFormDiv->find('css', 'label');
        $this->assertNotNull($profileFormDivLabel);

        $profileFormDivParent = $profileFormDivLabel->getParent();
        $this->assertNotNull($profileFormDivParent);

        $profileFormDivParent = $profileFormDivLabel->getParent();
        $this->assertEquals('something', $profileFormDivParent->getAttribute('data-custom'));

        $profileFormInput = $profileFormDivLabel->findField('user-name');
        $this->assertNotNull($profileFormInput);
        $this->assertEquals('username', $profileFormInput->getAttribute('name'));
    }

    public function testDeepTraversing()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $traversDivs = $this->getSession()->getPage()->findAll('css', 'div.travers');

        $this->assertCount(1, $traversDivs);

        $subDivs = $traversDivs[0]->findAll('css', 'div.sub');
        $this->assertCount(3, $subDivs);

        $this->assertTrue($subDivs[2]->hasLink('some deep url'));
        $this->assertFalse($subDivs[2]->hasLink('come deep url'));
        $subUrl = $subDivs[2]->findLink('some deep url');
        $this->assertNotNull($subUrl);

        $this->assertMatchesRegularExpression('/some_url$/', $subUrl->getAttribute('href'));
        $this->assertEquals('some deep url', $subUrl->getText());
        $this->assertEquals('some <strong>deep</strong> url', $subUrl->getHtml());

        $this->assertTrue($subUrl->has('css', 'strong'));
        $this->assertFalse($subUrl->has('css', 'em'));
        $this->assertEquals('deep', $subUrl->find('css', 'strong')->getText());
    }

    public function testFindingChild()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));

        $form = $this->getSession()->getPage()->find('css', 'footer form');
        $this->assertNotNull($form);

        $this->assertCount(1, $form->findAll('css', 'input'), 'Elements are searched only in the element, not in all previous matches');
    }
}
