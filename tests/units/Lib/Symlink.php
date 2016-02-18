<?php

namespace Solire\Install\test\units\Lib;

use mageekguy\atoum as Atoum;
use Solire\Install\Lib\Symlink as TestClass;

/**
 * Description of Symlink.
 *
 * @author thansen
 */
class Symlink extends Atoum
{
    private $target01 = TEST_TMP_DIR . DIRECTORY_SEPARATOR . 'target-1';
    private $link01 = TEST_TMP_DIR . DIRECTORY_SEPARATOR . 'a/b/link-1';

    private $falseTarget02 = TEST_TMP_DIR . DIRECTORY_SEPARATOR . 'target-2';
    private $link02 = TEST_TMP_DIR . DIRECTORY_SEPARATOR . 'c/d/link-2';

    private $errorLink03 = TEST_TMP_DIR . DIRECTORY_SEPARATOR . 'e/f/link-3';

    private $errorLink04 = TEST_TMP_DIR . DIRECTORY_SEPARATOR . 'g/link-4';

    public function setUp()
    {
        mkdir($this->target01, 0777, true);

        mkdir($this->errorLink03, 0777, true);

        mkdir(TEST_TMP_DIR . DIRECTORY_SEPARATOR . 'g', 0777, true);
        chmod(TEST_TMP_DIR . DIRECTORY_SEPARATOR . 'g', 0666);
    }

    public function tearDown()
    {
        @rmdir($this->target01);
        @unlink($this->link01);
        @rmdir(TEST_TMP_DIR . DIRECTORY_SEPARATOR . 'a/b');
        @rmdir(TEST_TMP_DIR . DIRECTORY_SEPARATOR . 'a');

        @rmdir($this->errorLink03);
        @rmdir(TEST_TMP_DIR . DIRECTORY_SEPARATOR . 'e/f');
        @rmdir(TEST_TMP_DIR . DIRECTORY_SEPARATOR . 'e');

        @rmdir(TEST_TMP_DIR . DIRECTORY_SEPARATOR . 'g');
    }

    public function testConstructor01()
    {
        $this
            ->if($symlink = new TestClass($this->target01, $this->link01))
            ->object($symlink)
                ->isInstanceOf('\Solire\Install\Lib\Symlink')
        ;

        return $symlink;
    }

    public function testConstructor02()
    {
        $this
            ->if($symlink = new TestClass($this->falseTarget02, $this->link02))
            ->object($symlink)
                ->isInstanceOf('\Solire\Install\Lib\Symlink')
        ;

        return $symlink;
    }

    public function testConstructor03()
    {
        $this
            ->if($symlink = new TestClass($this->target01, $this->errorLink03))
            ->object($symlink)
                ->isInstanceOf('\Solire\Install\Lib\Symlink')
        ;

        return $symlink;
    }

    public function testConstructor04()
    {
        $this
            ->if($symlink = new TestClass($this->target01, $this->errorLink04))
            ->object($symlink)
                ->isInstanceOf('\Solire\Install\Lib\Symlink')
        ;

        return $symlink;
    }

    public function testConstructor05()
    {
        $this
            ->if($symlink = new TestClass($this->target01, $this->link01, false))
            ->object($symlink)
                ->isInstanceOf('\Solire\Install\Lib\Symlink')
        ;

        return $symlink;
    }

    public function testConstructor06()
    {
        $this
            ->if($symlink = new TestClass($this->target01, $this->link01))
            ->object($symlink)
                ->isInstanceOf('\Solire\Install\Lib\Symlink')
        ;

        return $symlink;
    }

    public function testCreate01()
    {
        $symlink = $this->testConstructor01();

        $this
            ->boolean($symlink->create())
                ->isTrue()
        ;
    }

    public function testCreate02()
    {
        $symlink = $this->testConstructor02();

        $this
            ->boolean($symlink->create())
                ->isFalse()
        ;
    }

    public function testCreate03()
    {
        $symlink = $this->testConstructor03();

        $this
            ->exception(function () use ($symlink) {
                $symlink->create();
            })
                ->hasMessage('"' . TEST_TMP_DIR . '/e/f/link-3" existe déjà et n\'est pas un lien symbolique')
        ;
    }

    public function testCreate04()
    {
        $symlink = $this->testConstructor04();

        $this
            ->exception(function () use ($symlink) {
                $symlink->create();
            })
                ->hasMessage('La création du lien symbolique "' . TEST_TMP_DIR . '/g/link-4" vers "/var/www/solireFramework/install/tests/tmp/target-1" a échouée')
        ;
    }

    public function testCreate05()
    {
        $symlink = $this->testConstructor05();

        $this
            ->boolean($symlink->create())
                ->isFalse()
        ;
    }

    public function testCreate06()
    {
        $symlink = $this->testConstructor06();

        $this
            ->boolean($symlink->create())
                ->isTrue()
        ;
    }
}
