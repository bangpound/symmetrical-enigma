<?php

namespace App\Tests\Model;

use App\Model\KeyValue;
use PHPUnit\Framework\TestCase;

class KeyValueTest extends TestCase
{
    public function testKeyAccessor(): void
    {
        $kv = new KeyValue('folder/');
        $this->assertSame('folder/', $kv->getKey());

        $kv = new KeyValue('folder/item');
        $this->assertSame('folder/item', $kv->getKey());
    }

    public function testValueAccessorAndMutator(): void
    {
        $kv = new KeyValue('item');
        $this->assertNull($kv->getValue());

        $kv = new KeyValue('item');
        $kv->setValue('0');
        $this->assertSame('0', $kv->getValue());
    }

    /**
     *
     */
    public function testIsFolder(): void
    {
        $kv = new KeyValue('folder/');
        $this->assertTrue($kv->isFolder());

        $kv = new KeyValue('folder/item');
        $this->assertFalse($kv->isFolder());
    }

    /**
     *
     */
    public function testGetKeyParts(): void
    {
        $kv = new KeyValue('item');
        $this->assertEquals(['item'], $kv->getKeyParts());

        $kv = new KeyValue('folder/');
        $this->assertEquals(['folder'], $kv->getKeyParts());

        $kv = new KeyValue('folder/item');
        $this->assertEquals(['folder', 'item'], $kv->getKeyParts());

        $kv = new KeyValue('folder-a/folder-b/');
        $this->assertEquals(['folder-a', 'folder-b'], $kv->getKeyParts());

        $kv = new KeyValue('folder-a/folder-b/item');
        $this->assertEquals(['folder-a', 'folder-b', 'item'], $kv->getKeyParts());
    }

    /**
     *
     */
    public function testGetKeyWithoutParent(): void
    {
        $kv = new KeyValue('item');
        $this->assertEquals('item', $kv->getKeyWithoutParent());

        $kv = new KeyValue('folder/');
        $this->assertEquals('folder/', $kv->getKeyWithoutParent());

        $kv = new KeyValue('folder/item');
        $this->assertEquals('item', $kv->getKeyWithoutParent());

        $kv = new KeyValue('folder-a/folder-b/');
        $this->assertEquals('folder-b/', $kv->getKeyWithoutParent());
    }

    public function testIsLocked(): void
    {
        $kv = new KeyValue('folder/item');
        $this->assertFalse($kv->isLocked());

        $kv = new KeyValue('folder/item');
        $kv->setSession('edc0a210-33bc-49a7-9f22-b4ac3ec1f7cb');
        $this->assertTrue($kv->isLocked());
    }

    /**
     *
     */
    public function testGetParentKey(): void
    {
        $kv = new KeyValue('item');
        $this->assertEquals('', $kv->getParentKey());

        $kv = new KeyValue('folder/');
        $this->assertEquals('', $kv->getParentKey());

        $kv = new KeyValue('folder/item');
        $this->assertEquals('folder/', $kv->getParentKey());

        $kv = new KeyValue('folder-a/folder-b/');
        $this->assertEquals('folder-a/', $kv->getParentKey());

        $kv = new KeyValue('folder-a/folder-b/item');
        $this->assertEquals('folder-a/folder-b/', $kv->getParentKey());
    }

    /**
     *
     */
    public function testGetGrandParentKey(): void
    {
        $kv = new KeyValue('item');
        $this->assertEquals('', $kv->getGrandParentKey());

        $kv = new KeyValue('folder/');
        $this->assertEquals('', $kv->getGrandParentKey());

        $kv = new KeyValue('folder/item');
        $this->assertEquals('', $kv->getGrandParentKey());

        $kv = new KeyValue('folder-a/folder-b/');
        $this->assertEquals('', $kv->getGrandParentKey());

        $kv = new KeyValue('folder-a/folder-b/item');
        $this->assertEquals('folder-a/', $kv->getGrandParentKey());
    }
}
