<?php

namespace Behat\Mink\Tests\Driver\Js;

use Behat\Mink\Tests\Driver\TestCase;

class WindowTest extends TestCase
{
    public function testWindow()
    {
        $this->getSession()->visit($this->pathTo('/window.html'));
        $session = $this->getSession();
        $page = $session->getPage();
        $webAssert = $this->getAssertSession();

        $page->clickLink('Popup #1');
        $session->switchToWindow(null);

        $page->clickLink('Popup #2');
        $session->switchToWindow(null);

        $el = $webAssert->elementExists('css', '#text');
        $this->assertSame('Main window div text', $el->getText());

        $session->switchToWindow('popup_1');
        $el = $webAssert->elementExists('css', '#text');
        $this->assertSame('Popup#1 div text', $el->getText());

        $session->switchToWindow('popup_2');
        $el = $webAssert->elementExists('css', '#text');
        $this->assertSame('Popup#2 div text', $el->getText());

        $session->switchToWindow(null);
        $el = $webAssert->elementExists('css', '#text');
        $this->assertSame('Main window div text', $el->getText());
    }

    public function testGetWindowNames()
    {
        $this->getSession()->visit($this->pathTo('/window.html'));
        $session = $this->getSession();
        $page = $session->getPage();

        $windowName = $this->getSession()->getWindowName();

        $this->assertNotNull($windowName);

        $page->clickLink('Popup #1');
        $page->clickLink('Popup #2');

        $windowNames = $this->getSession()->getWindowNames();

        $this->assertNotNull($windowNames[0]);
        $this->assertNotNull($windowNames[1]);
        $this->assertNotNull($windowNames[2]);
    }

    public function testResizeWindow()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));
        $session = $this->getSession();

        $session->resizeWindow(400, 300);
        $session->wait(1000, 'false');
        $jsWindowSizeScript = <<<'JS'
        (function(){
          var boolSizeCheck = Math.abs(window.outerHeight - 300) <= 100 && Math.abs(window.outerWidth - 400) <= 100;
          if (boolSizeCheck){
            return true;
          }
          var w = window,
              d = document,
              e = d.documentElement,
              g = d.getElementsByTagName('body')[0],
              x = w.innerWidth || e.clientWidth || g.clientWidth,
              y = w.innerHeight|| e.clientHeight|| g.clientHeight;
          boolSizeCheck = Math.abs(y - 300) <= 100 && Math.abs(x - 400) <= 100;
          return boolSizeCheck;
        })();
JS;

        $this->assertTrue($session->evaluateScript($jsWindowSizeScript));
    }

    public function testWindowMaximize()
    {
        $this->getSession()->visit($this->pathTo('/index.html'));
        $session = $this->getSession();

        $session->maximizeWindow();
        $session->wait(1000, 'false');

        $script = 'return Math.abs(screen.availHeight - window.outerHeight);';

        $this->assertLessThanOrEqual(100, $session->evaluateScript($script));
    }
}
